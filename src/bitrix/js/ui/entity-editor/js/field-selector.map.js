{"version":3,"file":"field-selector.map.js","names":["BX","namespace","UI","EntityEditorFieldSelector","this","_id","_settings","_scheme","_excludedNames","_contentWrapper","_popup","fieldVisibleClass","fieldHiddenClass","_currentSchemeElementName","prototype","initialize","id","settings","prop","get","getArray","getMessage","name","getString","messages","isSchemeElementEnabled","schemeElement","getName","i","length","addClosingListener","listener","Event","EventEmitter","subscribe","removeClosingListener","unsubscribe","isOpened","isShown","setCurrentSchemeElementName","currentSchemeElementName","open","PopupWindow","autoHide","draggable","bindOptions","forceBindPosition","closeByEsc","closeIcon","zIndex","titleBar","content","prepareContent","lightShadow","contentNoPaddings","buttons","PopupWindowButton","text","message","className","events","click","delegate","onAcceptButtonClick","Button","color","Color","LIGHT","onCancelButtonClick","onPopupClose","bind","fieldsPopupItems","show","close","create","props","useFieldsSearch","headerWrapper","attrs","children","prepend","prepareContentHeaderSections","prepareContentHeaderSearch","container","columns","getElements","columnCount","column","sections","k","sectionCount","section","effectiveElements","elementChildren","j","childElement","isTransferable","push","sectionContainer","createSectionWrapper","getTitle","fillSectionElements","hiddenElements","hiddenSectionContainer","Loc","appendChild","headerSectionsWrapper","firstElementChild","buttonTitle","itemClass","headerSectionItem","html","input","onFilterSectionSearchInput","searchForm","onFilterSectionSearchInputClear","search","target","value","toLowerCase","getFieldsPopupItems","map","item","title","innerText","indexOf","removeClass","addClass","style","display","type","isArray","Array","from","querySelectorAll","prepareAnimation","onAnimationEnd","hasClass","getSelectedItems","results","checkBoxes","checkBox","checked","parts","split","sectionName","fieldName","emit","sender","isCanceled","items","destroy","onPopupDestroy","Tag","render","Text","encode","parentName","elements","forEach","itemId","itemWrapper","self","EntityEditorUserSelector","_isInitialized","_onlyUsers","getBoolean","getId","anchor","_mainWindow","SocNetLogDestination","containerWindow","init","extranetUser","userSearchArea","bindMainPopup","node","offsetTop","offsetLeft","callback","select","onSelect","unSelect","showSearchInput","departmentSelectDisable","users","groups","sonetgroups","socnetGroups","department","departmentRelation","buildDepartmentRelation","itemsLast","last","itemsSelected","getObject","isCrmFeed","useClientDatabase","destSort","allowAddUser","allowSearchCrmEmailUsers","allowUserSearch","openDialog","bindNode","closeDialog","bUndeleted","getFunction"],"sources":["field-selector.js"],"mappings":"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"}