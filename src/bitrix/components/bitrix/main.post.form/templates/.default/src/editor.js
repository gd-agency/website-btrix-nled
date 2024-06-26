import {Type, Dom, Runtime} from 'main.core';
import {EventEmitter, BaseEvent} from 'main.core.events';
import getKnownParser from './parsers/index';
import {bindAutoSave, bindHTML, bindToolbar,
	customizeHTMLEditor, showPanelEditor, showUrlPreview
} from './view-utils/index';
import Toolbar from './toolbar';
import TasksLimit from "./taskslimit";
import { Copilot } from 'ai.copilot';

export default class Editor
{
	static repo = new Map();
	id: string;
	name: ?string;
	eventNode: Element;
	toolbar: Toolbar;
	jobs: Map = new Map();
	copilot: Copilot;
	isCopilotLoaded: false;
	aiResultNodeAttr = 'bxhtmled-copilot-result-node';
	aiResultColor = '#8d52ec';
	copilotParams: Object;

	editorParams = {
		height: 100,
		ctrlEnterHandler: null,
		parsers: null,
		showPanelEditor: false,
		lazyLoad: true,
		urlPreviewId: null,
		tasksLimitExceeded: false,
	};
	actionQueue = [];

	constructor(
		options: {
			id: string,
			name: ?string,
			formId: string,
			eventNode: Element,
			copilotParams: Object,
		},
		editorParams: {
			height: 100,
			ctrlEnterHandler: null,
			showPanelEditor: false,
			lazyLoad: true,
			urlPreviewId: null,
			tasksLimitExceeded: false,
		}
	)
	{
		this.copilotParams = options.copilotParams;
		this.id = options['id'];
		this.name = options['name'];
		this.formId = options['formId'];
		this.eventNode = options.eventNode || document.querySelector('#div' + (this.name || this.id));
		this.eventNode.dataset.bxHtmlEditable = 'Y';
		this.formEntityType = null;
		Editor.repo.set(this.getId(), this);

		if (
			!Type.isArray(editorParams.parsers)
			&& Type.isPlainObject(editorParams.parsers)
		)
		{
			editorParams.parsers = Object.values(editorParams.parsers);
		}

		this.setEditorParams(editorParams);

		this.bindEvents(window['BXHtmlEditor'] ? window['BXHtmlEditor'].Get(this.getId()) : null);
		this.toolbar = new Toolbar(this.getEventObject(), this.getContainer());

		this.inited = true;

		if (this.name !== null)
		{
			window[this.name] = this;
		}

		BX.onCustomEvent(this, 'onInitialized', [this, this.getFormId()]);

		//region Compatibility for crm.timeline
		EventEmitter.subscribe(this.getEventObject(), 'OnFileUploadSuccess', ({compatData}) => {
			BX.onCustomEvent(this.getEventObject(), 'onFileIsAdded', compatData);
		});
		//endregion

		EventEmitter.subscribe(this.getEventObject(), 'onBusy', ({data: handler}) => {
			if (this.jobs.size <= 0)
			{
				EventEmitter.emit(this.getEventObject(), 'onLHEIsBusy');
			}
			this.jobs.set(handler, (this.jobs.get(handler) || 0) + 1);
		});

		EventEmitter.subscribe(this.getEventObject(), 'onReady', ({data: handler}) => {
			if (this.jobs.size <= 0 || !this.jobs.has(handler))
			{
				return;
			}
			let counter = this.jobs.get(handler);
			if (counter <= 1)
			{
				this.jobs.delete(handler);
				if (this.jobs.size <= 0 )
				{
					EventEmitter.emit(this.getEventObject(), 'onLHEIsReady');
				}
			}
			else
			{
				this.jobs.set(handler, --counter);
			}
		});
	}

	setEditorParams(editorParams)
	{
		this.editorParams = Object.assign(this.editorParams, editorParams);
	}

	bindEvents(htmlEditor = null)
	{
		this.events = {};
		[
			['OnEditorInitedBefore', this.OnEditorInitedBefore.bind(this)],
			['OnCreateIframeAfter', this.OnCreateIframeAfter.bind(this)],
			['OnEditorInitedAfter', this.OnEditorInitedAfter.bind(this)],
		].forEach(([eventName, closure]) => {
			if (!htmlEditor)
			{
				this.events[eventName] = (htmlEditor) => {
					if (htmlEditor.id === this.getId())
					{
						//!it important to use deprecated eventEmitter
						BX.removeCustomEvent(eventName, this.events[eventName]);
						delete this.events[eventName];
						closure(htmlEditor);
					}
				};
				//!it important to use deprecated eventEmitter
				BX.addCustomEvent(eventName, this.events[eventName]);
			}
			else
			{
				closure(htmlEditor);
			}
		});

		EventEmitter.subscribe(this.getEventObject(), 'OnShowLHE', this.OnShowLHE.bind(this));
		EventEmitter.subscribe(this.getEventObject(), 'OnButtonClick', this.OnButtonClick.bind(this));
		EventEmitter.subscribe(this.getEventObject(), 'OnParserRegister', ({data: parser}) => {this.addParser(parser);});
		EventEmitter.subscribe(this.getEventObject(), 'OnGetHTMLEditor', ({data: someObjectToReceiveHTMLEditor}) => {someObjectToReceiveHTMLEditor.htmlEditor = this.getEditor();});
		EventEmitter.subscribe(this.getEventObject(), 'OnInsertContent', ({data: [text, html]}) => { this.insertContent(text, html); });
		EventEmitter.subscribe(this.getEventObject(), 'OnAddButton', ({data: [button, beforeButton]}) => {
			this.getToolbar().insertAfter(button, beforeButton);
		});

		bindHTML(this);
	}

	getId()
	{
		return this.id;
	}

	setEditor(htmlEditor)
	{
		if (this.htmlEditor === htmlEditor)
		{
			return;
		}

		this.htmlEditor = htmlEditor;
		htmlEditor.formID = this.getFormId();

		EventEmitter.subscribe(htmlEditor, 'OnCtrlEnter', () => {
			htmlEditor.SaveContent();
			if (Type.isFunction(this.editorParams.ctrlEnterHandler))
			{
				this.editorParams.ctrlEnterHandler();
			}
			else if (Type.isStringFilled(this.editorParams.ctrlEnterHandler) && window[this.editorParams.ctrlEnterHandler])
			{
				window[this.editorParams.ctrlEnterHandler]();
			}
			else if (document.forms[this.getFormId()])
			{
				BX.submit(document.forms[this.getFormId()]);
			}
		});

		this.editorParams['height'] = htmlEditor.config['height'];

		console.groupCollapsed('main.post.form: parsers: ', this.getId());
		this.editorParams.parsers.forEach((parserId) => {
			const parser = getKnownParser(parserId, this, htmlEditor);
			if (parser)
			{
				console.groupCollapsed(parserId);
				console.log(parser);

				if (parser.hasButton())
				{
					htmlEditor.AddButton(parser.getButton())
				}
				htmlEditor.AddParser(parser.getParser());
				console.groupEnd(parserId);
			}
		});
		console.groupEnd('main.post.form: parsers: ', this.getId());

		//region Catching external files
		// paste an image from IO buffer into editor
		EventEmitter.subscribe(htmlEditor, 'OnImageDataUriHandle', ({compatData: [editor, imageBase64]}) => {
			const blob = BX.UploaderUtils.dataURLToBlob(imageBase64.src);

			if (blob && blob.size > 0 && blob.type.indexOf('image/') === 0)
			{
				EventEmitter.emit(this.getEventObject(), 'onShowControllers', 'show');
				blob.name = (blob.name || imageBase64.title || ('image.' + blob.type.substr(6)));
				blob.referrerToEditor = imageBase64;
				EventEmitter
					.emit(this.getEventObject(), 'OnImageHasCaught', new BaseEvent({data: blob}))
					.forEach((result: Promise) => {
						result
							.then(({image, html}) => {
								EventEmitter.emit(
									htmlEditor,
									'OnImageDataUriCaughtUploaded',
									new BaseEvent({compatData: [imageBase64, image, {replacement: html}]})
								);
							})
							.catch(() => {
								EventEmitter.emit(
									htmlEditor,
									'OnImageDataUriCaughtFailed',
									new BaseEvent({compatData: [imageBase64]})
								);
							})
					});
			}
		});

		// paste a video into editor
		EventEmitter.subscribe(EventEmitter.GLOBAL_TARGET, 'onAddVideoMessage', ({compatData: [file, formID]}) => {
			if (!formID || this.getFormId() !== formID)
			{
				return;
			}
			EventEmitter.emit(this.getEventObject(), 'onShowControllers', 'show');
			EventEmitter.emit(this.getEventObject(), 'OnVideoHasCaught', new BaseEvent({data: file}));
		});
		// DnD
		(() => {
			const placeHolder = BX('micro' + (this.name||this.id));
			let active = false;
			let timeoutId = 0;
			const activate = (e: MouseEvent) => {
				e.preventDefault();
				e.stopPropagation();
				if (timeoutId > 0)
				{
					clearTimeout(timeoutId);
					timeoutId = 0;
				}
				if (active === true)
				{
					return;
				}
				let isFileTransfer = (e && e['dataTransfer']
					&& e['dataTransfer']['types']
					&& e['dataTransfer']['types'].indexOf('Files') >= 0
				);
				if (isFileTransfer)
				{
					active = true;
					this.getContainer().classList.add('feed-add-post-dnd-over');
					if (placeHolder)
					{
						placeHolder.classList.add('feed-add-post-micro-dnd-ready');
					}
				}
				return true;
			};
			const disActivate = (e) => {
				e.preventDefault();
				e.stopPropagation();

				if (timeoutId > 0)
				{
					clearTimeout(timeoutId);
				}

				timeoutId = setTimeout(() => {
					active = false;
					this.getContainer().classList.remove('feed-add-post-dnd-over');
					if (placeHolder)
					{
						placeHolder.classList.remove('feed-add-post-micro-dnd-ready');
					}
				}, 100);
				return false;
			};
			const catchFiles = (e) => {
				disActivate(e);
				if (e
					&& e['dataTransfer']
					&& e['dataTransfer']['types']
					&& e['dataTransfer']['types'].indexOf('Files') >= 0
					&& e['dataTransfer']['files']
					&& e['dataTransfer']['files'].length > 0
				)
				{
					EventEmitter.emit(this.getEventObject(), 'OnShowLHE', new BaseEvent({compatData: ['justShow', {onShowControllers: 'show'}]}));
					EventEmitter.emit(this.getEventObject(), 'onFilesHaveCaught', new BaseEvent({data: e['dataTransfer']['files']}));
					EventEmitter.emit(this.getEventObject(), 'onFilesHaveDropped', { event: e });
				}
				return false;
			};

			this.getContainer().addEventListener('dragover', activate);
			this.getContainer().addEventListener('dragenter', activate);
			this.getContainer().addEventListener('dragleave', disActivate);
			this.getContainer().addEventListener('dragexit', disActivate);
			this.getContainer().addEventListener('drop', catchFiles);
			this.getContainer().setAttribute('dropzone', 'copy f:*\/*');
			if (!document.body.hasAttribute('dropzone'))
			{
				document.body.setAttribute('dropzone', 'copy f:*/*');
				document.body.addEventListener('dragover', function(e){
					e.preventDefault();
					e.stopPropagation();
					return true;
				});
				document.body.addEventListener('drop', function(e) {
					e.preventDefault();
					e.stopPropagation();
					if (e
						&& e['dataTransfer']
						&& e['dataTransfer']['types']
						&& e['dataTransfer']['types'].indexOf('Files') >= 0
						&& e['dataTransfer']['files']
						&& e['dataTransfer']['files'].length > 0
					)
					{
						let lhe;
						let iteratorBuffer;
						const iterator = this.constructor.#shownForms.keys();
						while (
							(iteratorBuffer = iterator.next())
							&& iteratorBuffer.done !== true
							&& iteratorBuffer.value
						)
						{
							lhe = iteratorBuffer.value;
						}
						if (lhe)
						{
							EventEmitter.emit(lhe.getEventObject(), 'OnShowLHE', new BaseEvent({compatData: ['justShow', {onShowControllers: 'show'}]}));
							EventEmitter.emit(lhe.getEventObject(), 'onFilesHaveCaught', new BaseEvent({ data: e['dataTransfer']['files']}));
							EventEmitter.emit(lhe.getEventObject(), 'onFilesHaveDropped', { event: e });
						}
					}
					return false;
				}.bind(this));
			}
			if (placeHolder)
			{
				placeHolder.addEventListener('dragenter', (e) => {
					activate(e);
					EventEmitter.emit(this.getEventObject(), 'OnShowLHE', new BaseEvent({compatData: ['justShow', {onShowControllers: 'show'}]}));
				});
			}

			EventEmitter.subscribe(this.getEditor(), 'OnIframeDrop', ({data: [e]}) => catchFiles(e));
			EventEmitter.subscribe(this.getEditor(), 'OnIframeDragOver', ({data: [e]}) => activate(e));
			EventEmitter.subscribe(this.getEditor(), 'OnIframeDragLeave', ({data: [e]}) => disActivate(e));
		})();
		//endregion

		EventEmitter.subscribe(htmlEditor, 'OnInsertContent', ({data: [text, html]}) => {
			this.insertContent(text, html);
		});

		//region Visible customization
		showPanelEditor(this, htmlEditor, this.editorParams);
		showUrlPreview(htmlEditor, this.editorParams);

		customizeHTMLEditor(this, htmlEditor);
		bindAutoSave(htmlEditor, BX(this.getFormId()));
		bindToolbar(this, htmlEditor);
		//endregion
		EventEmitter.subscribe(this.getEventObject(), 'OnAfterShowLHE', () => {
			this.getEditor().AllowBeforeUnloadHandler();
		});
		EventEmitter.subscribe(this.getEventObject(), 'OnAfterHideLHE', () => {
			TasksLimit.hidePopup();
			this.getEditor().DenyBeforeUnloadHandler();
		});

		EventEmitter.subscribe(
			htmlEditor,
			'OnIframeClick',
			() => {
				const event = new MouseEvent('click', {
					bubbles: true,
					cancelable: true,
					view: window,
				});
				htmlEditor.iframeView.container.dispatchEvent(event);
			}
		);
	}

	getEditor()
	{
		return this.htmlEditor;
	}

	getFormId()
	{
		return this.formId;
	}

	getEventObject()
	{
		return this.eventNode;
	}

	getContainer()
	{
		return this.eventNode;
	}

	getToolbar(): Toolbar
	{
		return this.toolbar;
	}

	OnEditorInitedBefore(htmlEditor)
	{
		this.setEditor(htmlEditor);
	}

	OnCreateIframeAfter()
	{
		if (this.editorIsLoaded !== true)
		{
			this.editorIsLoaded = true;
			this.exec();
			EventEmitter.emit(this, 'OnEditorIsLoaded', []);
		}
	}

	get isReady()
	{
		return this.editorIsLoaded;
	}

	OnEditorInitedAfter(htmlEditor)
	{
		if (!this.editorParams.lazyLoad)
		{
			EventEmitter.emit(this.getEventObject(),  'OnShowLHE', new BaseEvent({compatData: ['justShow', htmlEditor, false]}));
		}

		if (htmlEditor.sandbox && htmlEditor.sandbox.inited)
		{
			this.OnCreateIframeAfter();
		}
	}

	addParser(parser: {
		id: string,
		init: Function, // init(htmlEditor) {} // function to catch htmlEditor
		parse: Function, // parse(text) {}
		unparse: Function, // unparse(bxTag, oNode) {}
	})
	{
		this.exec(() => {
			parser.init(this.getEditor());
			this.getEditor().AddParser({
				name: parser.id,
				obj: {
					Parse: (parserId, text) => {
						return parser.parse(text)
					},
					UnParse: parser.unparse
				}
			});
			if (!this['addParserAfterDebounced'])
			{
				this.addParserAfterDebounced = Runtime.debounce(() => {
					const content = this.getEditor().GetContent();
					if (/&#9[13];/gi.test(content))
					{
						this.getEditor().SetContent(
							content.replace(/&#91;/ig, "[").replace(/&#93;/ig, "]"),
							true,
						);
					}
				}, 100);
			}
			this.addParserAfterDebounced();
		});
	}

	insertContent(text, html: ?string = null)
	{
		this.exec(() => {
			const editorMode = this.getEditor().GetViewMode();
			if (editorMode === 'wysiwyg')
			{
				const range = this.getEditor().selection.GetRange();
				this.getEditor().InsertHtml(html || text, range);
				setTimeout(this.getEditor().AutoResizeSceleton.bind(this.getEditor()), 500);
				setTimeout(this.getEditor().AutoResizeSceleton.bind(this.getEditor()), 1000);
			}
			else
			{
				this.getEditor().textareaView.Focus();

				if (!this.getEditor().bbCode)
				{
					const doc = this.getEditor().GetIframeDoc();
					const dummy = doc.createElement('DIV');
					dummy.style.display = 'none';
					dummy.innerHTML = text;
					doc.body.appendChild(dummy);

					text = this.getEditor().Parse(text, true, false);

					dummy.parentNode.removeChild(dummy);
				}

				this.getEditor().textareaView.WrapWith('', '', text);
			}
		});
	}

	reinit(text, data)
	{
		let showControllers = 'hide';
		if (Type.isPlainObject(data) && Object.values(data).length)
		{
			Object.values(data).forEach((property) => {
				if (property && property['VALUE'])
				{
					showControllers = 'show';
				}
			})
		}

		EventEmitter.emitAsync(this.getEventObject(), 'onReinitializeBeforeAsync', [text, data]).then(() => {
			EventEmitter.emit(this.getEventObject(), 'onShowControllers', showControllers);
			EventEmitter.emit(this.getEventObject(),  'onReinitializeBefore', [text, data]);
			this.getEditor().CheckAndReInit(Type.isString(text) ? text : '');
			BX.onCustomEvent(this.getEditor(), 'onReinitialize', [this, text, data]);

			if (this.editorParams['height'])
			{
				this.oEditor.SetConfigHeight(this.editorParams['height']);
				this.oEditor.ResizeSceleton();
			}
		});
	}

	OnShowLHE({data, compatData})
	{
		let [show, setFocus, FCFormId] = data || compatData;
		if (!this.getEditor() && window['BXHtmlEditor'])
		{
			window['BXHtmlEditor'].Get(this.getId()).Init();
		}
		show = (show === false || show === 'hide' || show === 'justShow') ? show : true;

		const placeHolder = BX('micro' + (this.name||this.id));
		if (placeHolder)
		{
			placeHolder.style.display = ((show === true || show === 'justShow') ? 'none' : 'block');
		}

		if (show === 'hide')
		{
			this.constructor.#shownForms.delete(this);

			EventEmitter.emit(this.getEventObject(), 'OnBeforeHideLHE');
			if (this.getContainer().style.display === 'none')
			{
				EventEmitter.emit(this.getEventObject(), 'OnAfterHideLHE');
				EventEmitter.emit(this.getEventObject(), 'onShowControllers', 'hide');
			}
			else
			{
				(new BX['easing']({
					duration : 200,
					start : { opacity: 100, height : this.getContainer().scrollHeight},
					finish : { opacity : 0, height : 20},
					transition : BX.easing.makeEaseOut(BX.easing.transitions.quad),
					step : (state) => {
						this.getContainer().style.height = state.height + 'px';
						this.getContainer().style.opacity = state.opacity / 100;
					},
					complete: () => {
						this.getContainer().style.cssText = '';
						this.getContainer().style.display = 'none';
						EventEmitter.emit(this.getEventObject(), 'OnAfterHideLHE');
						EventEmitter.emit(this.getEventObject(), 'onShowControllers', 'hide');
					}
				})).animate();
			}
		}
		else if (show)
		{
			this.constructor.#shownForms.set(this);

			this.formEntityType = (
				Type.isArray(FCFormId)
				&& Type.isStringFilled(FCFormId[0])
				&& FCFormId[0].match(/^TASK_(\d+)$/i)
					? 'task'
					: null
			);

			if (setFocus && Type.isPlainObject(setFocus))
			{
				if (setFocus['onShowControllers'])
				{
					EventEmitter.emit(this.getEventObject(), 'onShowControllers', setFocus['onShowControllers']);
				}
			}

			EventEmitter.emit(this.getEventObject(), 'OnBeforeShowLHE');
			if (show === 'justShow' || this.getContainer().style.display === 'block')
			{
				this.getContainer().style.display = 'block';
				EventEmitter.emit(this.getEventObject(), 'OnAfterShowLHE'); //To remember: Here is set a text -> reinitData-> reinit -> editor.CheckAndReInit()
				if (setFocus !== false)
				{
					this.getEditor().Focus();
				}
			}
			else
			{
				Dom.adjust(this.getContainer(), {
					style: {
						display: 'block',
						overflow: 'hidden',
						height: '20px',
						opacity:0.1
					}
				});
				(new BX['easing']({
					duration: 200,
					start: { opacity: 10, height: 20 },
					finish: { opacity: 100, height: this.getContainer().scrollHeight},
					transition: BX.easing.makeEaseOut(BX.easing.transitions.quad),
					step: (state) => {
						this.getContainer().style.height = state.height + 'px';
						this.getContainer().style.opacity = state.opacity / 100;
					},
					complete: () => {
						EventEmitter.emit(this.getEventObject(),  'OnAfterShowLHE'); //To remember: Here is set a text -> reinitData-> reinit -> editor.CheckAndReInit()
						this.getEditor().Focus();
						this.getContainer().style.cssText = "";
					}
				})).animate();
			}
		}
		else
		{
			this.constructor.#shownForms.delete(this);

			EventEmitter.emit(this.getEventObject(), 'OnBeforeHideLHE');
			EventEmitter.emit(this.getEventObject(), 'onShowControllers', 'hide');
			this.getContainer().style.display = 'none';
			EventEmitter.emit(this.getEventObject(), 'OnAfterHideLHE');
		}

		if (FCFormId && this.copilotParams)
		{
			this.copilotParams.contextParameters = {
				xmlId: FCFormId[0],
				entityId: FCFormId[1],
			};
		}
	}

	OnButtonClick({data: [action]})
	{
		if (action !== 'cancel')
		{
			const res = {result : true};
			EventEmitter.emit(this.getEventObject(),  'OnClickBeforeSubmit', new BaseEvent({compatData: [this, res]}));
			if (res['result'] !== false)
			{
				EventEmitter.emit(this.getEventObject(),  'OnClickSubmit',  new BaseEvent({compatData: [this]}));
			}
		}
		else
		{
			EventEmitter.emit(this.getEventObject(),  'OnClickCancel',  new BaseEvent({compatData: [this]}));
			EventEmitter.emit(this.getEventObject(),  'OnShowLHE',  new BaseEvent({compatData: ['hide']}));
		}
	}

	//region compatibility
	exec(func, args)
	{
		if (typeof func == 'function')
		{
			this.actionQueue.push([func, args]);
		}

		if (this.editorIsLoaded === true)
		{
			let res;
			while ((res = this.actionQueue.shift()) && res)
			{
				res[0].apply(this, res[1]);
			}
		}
	}

	get oEditor()
	{
		return this.getEditor();
	}

	get oEditorId()
	{
		return this.getId();
	}

	get formID()
	{
		return this.getFormId();
	}

	get params()
	{
		return {
			formID: this.getFormId()
		};
	}

	showPanelEditor()
	{
		showPanelEditor(this, this.getEditor(), {});
	}

	getContent()
	{
		return (this.oEditor ? this.oEditor.GetContent() : '');
	}

	setContent(text)
	{
		if (this.getEditor())
		{
			this.getEditor().SetContent(text);
		}
	}

	controllerInit(status)
	{
		EventEmitter.emit(this.getEventObject(), 'onShowControllers', status === 'hide' ? 'hide' : 'show');
	}

	initCopilot(): void
	{
		this.copilot = new BX.AI.Copilot({
			moduleId: this.copilotParams.moduleId,
			contextId: this.copilotParams.contextId,
			category: this.copilotParams.category,
			autoHide: true,
			prompts: this.copilotParams.prompts,
			engines: this.copilotParams.engines,
			contextParameters: this.copilotParams.contextParameters,
		});

		this.copilot.subscribe('aiResult', this.aiResultHandler.bind(this));
		this.copilot.subscribe('save', this.saveHandler.bind(this));
		this.copilot.subscribe('cancel', this.removeResult.bind(this));
		this.copilot.subscribe('hide', this.removeResult.bind(this));
		window.addEventListener('resize', this.handleResizeWindow.bind(this));
		document.addEventListener('keydown', this.onWindowKeyDownHandler.bind(this));

		this.copilot.init();
	}

	showCopilot(): void
	{
		if (!BX.AI?.Copilot)
		{
			return;
		}

		if (!this.copilot)
		{
			this.initCopilot();
		}

		this.contentContainer = this.htmlEditor.iframeView.container;
		this.contentEditable = this.htmlEditor.iframeView.element;

		this.copilot.setContext(this.contentEditable.innerText.replaceAll('\n', ''));

		this.emptyLine = BX.Tag.render`<div></div>`;
		this.contentEditable.append(this.emptyLine);

		this.copilot.show({
			bindElement: this.getBindPosition(),
			width: this.contentEditable.offsetWidth - 30,
		});
	}

	getBindPosition(): {top: number, left: number}
	{
		return {
			top: this.contentContainer.getBoundingClientRect().y + this.emptyLine.getBoundingClientRect().y + window.scrollY,
			left: this.contentContainer.getBoundingClientRect().x + this.emptyLine.getBoundingClientRect().x,
		};
	}

	getResultNodes()
	{
		return [...this.contentEditable.querySelectorAll(`[${this.aiResultNodeAttr}=true]`)];
	}

	aiResultHandler(event): void
	{
		const resultNodes = this.getResultNodes();

		let lastResultSpan = null;
		if (resultNodes.length === 0)
		{
			lastResultSpan = this.getSpanWithText(event.data.result);
			BX.Dom.attr(lastResultSpan, this.aiResultNodeAttr, 'true');
			BX.Dom.style(lastResultSpan, 'color', this.aiResultColor);
			BX.Dom.append(lastResultSpan, this.contentEditable);
		}
		else
		{
			lastResultSpan = [...resultNodes].pop();
			lastResultSpan.innerText = event.data.result;
		}

		this.htmlEditor.iframeView.ScrollToInsertedText();

		let newPosition = this.getBindPosition();
		newPosition.top += lastResultSpan.offsetHeight + 5;

		this.copilot.adjustPosition(newPosition);
	}

	getSpanWithText(text): HTMLSpanElement
	{
		const span = BX.Dom.create('span');
		span.innerText = text;

		return span;
	}

	saveHandler(event)
	{
		if (this.emptyLine && Type.isDomNode(this.emptyLine))
		{
			Dom.remove(this.emptyLine);
		}

		if (this.getResultNodes().length === 0)
		{
			const selection = this.htmlEditor.iframeView.GetSelection().getRangeAt(0);
			const span = this.getSpanWithText(event.data.result);
			selection.deleteContents();
			selection.insertNode(span);
			this.htmlEditor.iframeView.UpdateHeight();
		}

		for (const resultNode of this.getResultNodes())
		{
			for (const childNode of resultNode.childNodes)
			{
				resultNode.before(childNode.cloneNode(true));
			}

			resultNode.remove();
		}

		this.htmlEditor.iframeView.editor.synchro.FromIframeToTextarea(true, true);
		this.htmlEditor.iframeView.editor.synchro.FromTextareaToIframe(true);

		this.copilot.hide();
	}

	removeResult(): void
	{
		if (this.emptyLine && Type.isDomNode(this.emptyLine))
		{
			Dom.remove(this.emptyLine);
		}

		this.getResultNodes().forEach((resultNode) => resultNode.remove());
	}

	handleResizeWindow(): void
	{
		this.copilot.adjustPosition(this.getBindPosition());
		this.copilot.adjustWidth(this.contentEditable.offsetWidth - 30);
	}

	onWindowKeyDownHandler(event): void
	{
		if (event.key === 'Escape')
		{
			this.copilot.hide();
		}
	}

	get controllers()
	{
		const event = new BaseEvent();
		const data = {};
		event.setData(data);
		EventEmitter.emit(this.getEventObject(), 'onCollectControllers', event);
		const result = {};
		Object.keys(data).forEach((fieldName) => {
			result[fieldName] = Object.assign({}, data[fieldName]);
			result[fieldName]['values'] = {};
			if (Type.isArray(data[fieldName]['values']))
			{
				data[fieldName]['values'].forEach((id) => {
					result[fieldName]['values'][id] = {
						id : id,
					};
				});
			}
			else if (Type.isPlainObject(data[fieldName]['values']))
			{
				result[fieldName]['values'] =  Object.assign({}, data[fieldName]['values']);
			}
		});
		return result;
	}

	get arFiles()
	{
		const event = new BaseEvent();
		const data = {};
		event.setData(data);
		EventEmitter.emit(this.getEventObject(), 'onCollectControllers', event);
		const result = {};
		Object.keys(data).forEach((fieldName) => {
			if (data[fieldName]['values'])
			{
				data[fieldName]['values'].forEach((id) => {
					result[id] = [fieldName];
				});
			}
		});
		return result;
	}
	//endregion
	static #shownForms = new Map();
}
