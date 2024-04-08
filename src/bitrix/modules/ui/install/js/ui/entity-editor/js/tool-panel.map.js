{"version":3,"sources":["tool-panel.js"],"names":["BX","namespace","UI","EntityEditorToolPanel","this","_id","_settings","_container","_wrapper","_editor","_isVisible","_isLocked","_hasLayout","_keyPressHandler","delegate","onKeyPress","_customButtons","_buttonsOrder","VIEW","EDIT","EntityEditorActionIds","defaultActionId","cancelActionId","prototype","initialize","id","settings","type","isNotEmptyString","util","getRandomString","prop","getElementNode","get","getBoolean","customButtons","getArray","i","length","customButtonProps","ID","createCustomButton","buttonsOrder","getObject","editButtonsOrder","viewButtonsOrder","attachToEditorEvents","addCustomEvent","eventsNamespace","editor","additionalData","control","getMode","EntityEditorMode","edit","showEditModeButtons","showViewModeButtons","_viewModeSectionControl","hide","_editModeSectionControl","show","getId","getContainer","setContainer","container","isVisible","setVisible","visible","adjustLayout","isLocked","setLocked","locked","activeButton","_editButton","_clickedButton","addClass","removeClass","disableSaveButton","buttonsToDisable","_this","Object","keys","forEach","button","includes","push","disabled","onCustomEvent","window","enableSaveButton","buttonsToEnable","isSaveButtonEnabled","layout","create","props","className","title","text","message","events","click","onSaveButtonClick","_cancelButton","attrs","href","onCancelButtonClick","_errorContainer","style","maxHeight","sectionControlChildren","editModeButtonsOrder","viewModeButtonsOrder","editModeSectionControlChildren","viewModeSectionControlChildren","children","appendChild","unbind","document","bind","buttonProps","actionId","ACTION_ID","CLASS","htmlspecialchars","TEXT","onCustomButtonClick","dataset","getPosition","pos","e","target","saveChanged","performAction","cancel","eventReturnFalse","isFunction","PopupWindowManager","isAnyPopupShown","event","keyCode","eventCancelBubble","ctrlKey","addError","error","Type","isStringFilled","replace","split","map","line","Text","encode","join","html","clearErrors","innerHTML","getMessage","name","m","messages","hasOwnProperty","self"],"mappings":"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","file":"tool-panel.map.js"}