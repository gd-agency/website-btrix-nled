{"version":3,"file":"bundle.map.js","names":["this","BX","exports","ui_designTokens","main_popup","ui_vue3","ui_switcher","ui_forms","main_core_events","main_core","CheckboxListSections","props","methods","handleClick","key","$emit","getSectionsItemClassName","sectionValue","template","CheckboxListCategory","handleCheckBox","id","getOptionClassName","optionValue","Content","components","data","dataSections","sections","dataCategories","categories","dataOptions","options","dataCompactField","compactField","search","longContent","scrollIsBottom","scrollIsTop","renderSwitcher","switcher","UI","Switcher","node","$refs","checked","value","size","handlers","toggled","handleSwitcherToggled","handleCheckBoxToggled","item","find","option","clearSearch","handleClearSearchButtonClick","searchInput","focus","handleSectionsToggled","section","getOptionsByCategory","category","optionsByTitle","filter","categoryKey","getCheckedOptionsId","map","checkLongContent","container","clientHeight","scrollHeight","getBottomIndent","scrollTop","getTopIndent","handleScroll","handleSearchEscKeyUp","defaultSettings","defaultValue","click","forEach","Array","isArray","selectAll","categoryBySection","deselectAll","cancel","popup","destroy","apply","EventEmitter","emit","dialog","fields","watch","$nextTick","computed","visibleOptions","length","sectionKey","isEmptyContent","isSearchDisabled","some","isCheckedCheckboxes","title","toLowerCase","indexOf","Type","isArrayFilled","wrapperClassName","searchClassName","applyClassName","SwitcherText","isStringFilled","lang","Loc","getMessage","placeholderText","placeholder","defaultSettingsBtnText","defaultBtn","applyBtnText","acceptBtn","cancelBtnText","cancelBtn","selectAllBtnText","selectAllBtn","deselectAllBtnText","deselectAllBtn","emptyStateTitleText","emptyStateDescriptionText","mounted","CheckboxList","constructor","super","setEventNamespace","subscribeFromOptions","events","Error","isPlainObject","columnCount","isNumber","popupOptions","getPopup","Dom","create","addClass","Popup","className","width","overlay","autoHide","minHeight","borderRadius","contentPadding","contentBackground","animation","titleBar","content","closeIcon","closeByEsc","BitrixVue","createApp","mount","show","hide","Main","Vue3","Event"],"sources":["bundle.js"],"mappings":"AACAA,KAAKC,GAAKD,KAAKC,IAAM,CAAC,GACrB,SAAUC,EAAQC,EAAgBC,EAAWC,EAAQC,EAAYC,EAASC,EAAiBC,GAC3F,aAEA,MAAMC,EAAuB,CAC3BC,MAAO,CAAC,YACRC,QAAS,CACPC,YAAYC,GACVd,KAAKe,MAAM,iBAAkBD,EAC/B,EACAE,yBAAyBC,GACvB,MAAO,CAAC,kCAAmC,CACzC,YAAaA,GAEjB,GAEFC,SAAU,kYAgBZ,MAAMC,EAAuB,CAC3BR,MAAO,CAAC,cAAe,WAAY,WACnCC,QAAS,CACPQ,eAAeC,GACbrB,KAAKe,MAAM,eAAgBM,EAC7B,EACAC,mBAAmBC,GACjB,MAAO,CAAC,SAAU,kBAAmB,qCAAsC,CACzE,YAAaA,GAEjB,GAEFL,SAAU,49BAkCZ,MAAMM,EAAU,CACdC,WAAY,CACVf,uBACAS,wBAEFR,MAAO,CAAC,SAAU,QAAS,cAAe,eAAgB,OAAQ,WAAY,aAAc,WAC5Fe,OACE,MAAO,CACLC,aAAc3B,KAAK4B,SACnBC,eAAgB7B,KAAK8B,WACrBC,YAAa/B,KAAKgC,QAClBC,iBAAkBjC,KAAKkC,aACvBC,OAAQ,GACRC,YAAa,MACbC,eAAgB,KAChBC,YAAa,MAEjB,EACA1B,QAAS,CACP2B,iBACE,GAAIvC,KAAKiC,iBAAkB,CACzB,MAAMO,EAAW,IAAIvC,GAAGwC,GAAGC,SAAS,CAClCC,KAAM3C,KAAK4C,MAAMJ,SACjBK,QAAS7C,KAAKiC,iBAAiBa,MAC/BC,KAAM,QACNC,SAAU,CACRC,QAAS,IAAMjD,KAAKkD,0BAG1B,CACF,EACAA,wBACElD,KAAKiC,iBAAiBa,OAAS9C,KAAKiC,iBAAiBa,KACvD,EACAK,sBAAsB9B,GACpB,MAAM+B,EAAOpD,KAAK+B,YAAYsB,MAAKC,GAAUA,EAAOjC,KAAOA,IAC3D,GAAI+B,EAAM,CACRA,EAAKN,OAASM,EAAKN,KACrB,CACF,EACAS,cACEvD,KAAKmC,OAAS,EAChB,EACAqB,+BACExD,KAAK4C,MAAMa,YAAYC,QACvB1D,KAAKuD,aACP,EACAI,sBAAsB7C,GACpB,MAAM8C,EAAU5D,KAAK2B,aAAa0B,MAAKO,GAAWA,EAAQ9C,MAAQA,IAClE,GAAI8C,EAAS,CACXA,EAAQd,OAASc,EAAQd,KAC3B,CACF,EACAe,qBAAqBC,GACnB,OAAO9D,KAAK+D,eAAeC,QAAOZ,GAAQA,EAAKa,cAAgBH,GACjE,EACAI,sBACE,OAAOlE,KAAK+B,YAAYiC,QAAOV,GAAUA,EAAOR,QAAU,OAAMqB,KAAIb,GAAUA,EAAOjC,IACvF,EACA+C,mBACE,GAAIpE,KAAK4C,MAAMyB,UAAW,CACxBrE,KAAKoC,YAAcpC,KAAK4C,MAAMyB,UAAUC,aAAetE,KAAK4C,MAAMyB,UAAUE,YAC9E,KAAO,CACLvE,KAAKoC,YAAc,KACrB,CACF,EACAoC,kBACExE,KAAKqC,iBAAmBrC,KAAK4C,MAAMyB,UAAUI,UAAYzE,KAAK4C,MAAMyB,UAAUC,cAAgBtE,KAAK4C,MAAMyB,UAAUE,aAAe,GACpI,EACAG,eACE1E,KAAKsC,YAActC,KAAK4C,MAAMyB,UAAUI,SAC1C,EACAE,eACE3E,KAAKwE,kBACLxE,KAAK0E,cACP,EACAE,uBACE5E,KAAK4C,MAAMyB,UAAUX,QACrB1D,KAAKuD,aACP,EACAsB,kBACE7E,KAAKuD,cACL,GAAIvD,KAAKiC,kBAAoBjC,KAAKiC,iBAAiBa,QAAU9C,KAAKiC,iBAAiB6C,aAAc,CAC/F9E,KAAK4C,MAAMJ,SAASuC,OACtB,CACA/E,KAAK+B,YAAYiD,SAAQ1B,GAAUA,EAAOR,MAAQQ,EAAOwB,eACzD,GAAIG,MAAMC,QAAQlF,KAAK2B,cAAe,CACpC3B,KAAK2B,aAAaqD,SAAQpD,GAAYA,EAASkB,MAAQ,MACzD,CACF,EACAqC,YACEnF,KAAKoF,kBAAkBJ,SAAQlB,IAC7B9D,KAAK6D,qBAAqBC,EAAShD,KAAKkE,SAAQ1B,GAAUA,EAAOR,MAAQ,MAAK,GAElF,EACAuC,cACErF,KAAKoF,kBAAkBJ,SAAQlB,IAC7B9D,KAAK6D,qBAAqBC,EAAShD,KAAKkE,SAAQ1B,GAAUA,EAAOR,MAAQ,OAAM,GAEnF,EACAwC,SACEtF,KAAKuF,MAAMC,SACb,EACAC,QACEjF,EAAiBkF,aAAaC,KAAK3F,KAAK4F,OAAQ,UAAW,CACzDpD,SAAUxC,KAAKiC,iBACf4D,OAAQ7F,KAAKkE,wBAEflE,KAAKuF,MAAMC,SACb,GAEFM,MAAO,CACL3D,SACEnC,KAAK+F,WAAU,KACb/F,KAAKoE,kBAAkB,GAE3B,EACAgB,oBACEpF,KAAK+F,WAAU,KACb/F,KAAKoE,kBAAkB,GAE3B,GAEF4B,SAAU,CACRC,iBACE,IAAKhB,MAAMC,QAAQlF,KAAK2B,gBAAkB3B,KAAK2B,aAAauE,OAAQ,CAClE,OAAOlG,KAAK+D,cACd,CACA,OAAO/D,KAAK+D,eAAeC,QAAOV,IAChC,MAAMQ,EAAW9D,KAAK6B,eAAewB,MAAKS,GAAYA,EAAShD,MAAQwC,EAAOW,cAC9E,MAAML,EAAU5D,KAAK2B,aAAa0B,MAAKO,GAAWA,EAAQ9C,MAAQgD,EAASqC,aAC3E,OAAOvC,GAAW,UAAY,EAAIA,EAAQd,KAAK,GAEnD,EACAsD,iBACE,OAAOpG,KAAKiG,eAAeC,OAAS,CACtC,EACAG,mBACE,GAAIrG,KAAK2B,cAAgB3B,KAAK2B,aAAauE,OAAQ,CACjD,OAAQlG,KAAK2B,aAAa2E,MAAK1C,GAAWA,EAAQd,OACpD,CACA,OAAO,KACT,EACAyD,sBACE,OAAQvG,KAAK+B,YAAYiC,QAAOV,GAAUA,EAAOR,QAAU,OAAMoD,MACnE,EACAnC,iBACE,OAAO/D,KAAK+B,YAAYiC,QAAOZ,GAAQA,EAAKoD,MAAMC,cAAcC,QAAQ1G,KAAKmC,OAAOsE,kBAAoB,GAC1G,EACArB,oBACE,IAAKH,MAAMC,QAAQlF,KAAK2B,gBAAkBlB,EAAUkG,KAAKC,cAAc5G,KAAK2B,cAAe,CACzF,OAAO3B,KAAK6B,cACd,CACA,OAAO7B,KAAK6B,eAAemC,QAAOF,IAChC,MAAMF,EAAU5D,KAAK2B,aAAa0B,MAAKO,GAAWE,EAASqC,aAAevC,EAAQ9C,MAClF,OAAO8C,GAAW,UAAY,EAAIA,EAAQd,KAAK,GAEnD,EACA+D,mBACE,MAAO,CAAC,4BAA6B,CACnC,SAAU7G,KAAKoC,aACd,CACD,WAAYpC,KAAKqC,gBAChB,CACD,QAASrC,KAAKsC,aAElB,EACAwE,kBACE,MAAO,CAAC,2BAA4B,CAClC,aAAc9G,KAAKqG,kBAEvB,EACAU,iBACE,MAAO,CAAC,wBAAyB,CAC/B,kBAAmB/G,KAAKuG,qBAE5B,EACAS,eACE,OAAOvG,EAAUkG,KAAKM,eAAejH,KAAKkH,KAAK1E,UAAYxC,KAAKkH,KAAK1E,SAAW/B,EAAU0G,IAAIC,WAAW,6CAC3G,EACAC,kBACE,OAAO5G,EAAUkG,KAAKM,eAAejH,KAAKkH,KAAKI,aAAetH,KAAKkH,KAAKI,YAAc7G,EAAU0G,IAAIC,WAAW,gDACjH,EACAG,yBACE,OAAO9G,EAAUkG,KAAKM,eAAejH,KAAKkH,KAAKM,YAAcxH,KAAKkH,KAAKM,WAAa/G,EAAU0G,IAAIC,WAAW,oCAC/G,EACAK,eACE,OAAOhH,EAAUkG,KAAKM,eAAejH,KAAKkH,KAAKQ,WAAa1H,KAAKkH,KAAKQ,UAAYjH,EAAU0G,IAAIC,WAAW,yCAC7G,EACAO,gBACE,OAAOlH,EAAUkG,KAAKM,eAAejH,KAAKkH,KAAKU,WAAa5H,KAAKkH,KAAKU,UAAYnH,EAAU0G,IAAIC,WAAW,yCAC7G,EACAS,mBACE,OAAOpH,EAAUkG,KAAKM,eAAejH,KAAKkH,KAAKY,cAAgB9H,KAAKkH,KAAKY,aAAerH,EAAU0G,IAAIC,WAAW,sCACnH,EACAW,qBACE,OAAOtH,EAAUkG,KAAKM,eAAejH,KAAKkH,KAAKc,gBAAkBhI,KAAKkH,KAAKc,eAAiBvH,EAAU0G,IAAIC,WAAW,iDACvH,EACAa,sBACE,OAAOxH,EAAU0G,IAAIC,WAAW,sDAClC,EACAc,4BACE,OAAOzH,EAAU0G,IAAIC,WAAW,4DAClC,GAEFe,UACEnI,KAAKuC,iBACLvC,KAAK+F,WAAU,KACb/F,KAAKoE,kBAAkB,GAE3B,EACAlD,SAAU,66GA2HZ,MAAMkH,UAAqB5H,EAAiBkF,aAC1C2C,YAAYrG,GACVsG,QACAtI,KAAKuI,kBAAkB,8BACvBvI,KAAKwI,qBAAqBxG,EAAQyG,QAClC,IAAKhI,EAAUkG,KAAKC,cAAc5E,EAAQF,YAAa,CACrD,MAAM,IAAI4G,MAAM,oDAClB,CACA1I,KAAK8B,WAAaE,EAAQF,WAC1B,IAAKrB,EAAUkG,KAAKC,cAAc5E,EAAQA,SAAU,CAClD,MAAM,IAAI0G,MAAM,iDAClB,CACA1I,KAAKgC,QAAUA,EAAQA,QACvBhC,KAAKkC,aAAezB,EAAUkG,KAAKgC,cAAc3G,EAAQE,cAAgBF,EAAQE,aAAe,KAChGlC,KAAK4B,SAAWnB,EAAUkG,KAAKzB,QAAQlD,EAAQJ,UAAYI,EAAQJ,SAAW,KAC9E5B,KAAKkH,KAAOzG,EAAUkG,KAAKgC,cAAc3G,EAAQkF,MAAQlF,EAAQkF,KAAO,CAAC,EACzElH,KAAKuF,MAAQ,KACbvF,KAAK4I,YAAcnI,EAAUkG,KAAKkC,SAAS7G,EAAQ4G,aAAe5G,EAAQ4G,YAAc,EACxF5I,KAAK8I,aAAerI,EAAUkG,KAAKgC,cAAc3G,EAAQ8G,cAAgB9G,EAAQ8G,aAAe,CAAC,CACnG,CACAC,WACE,MAAM1E,EAAY5D,EAAUuI,IAAIC,OAAO,OACvCxI,EAAUuI,IAAIE,SAAS7E,EAAW,mCAClC,IAAKrE,KAAKuF,MAAO,CACfvF,KAAKuF,MAAQ,IAAInF,EAAW+I,MAAM,CAChCC,UAAW,yBACXC,MAAO,IACPC,QAAS,KACTC,SAAU,KACVC,UAAW,IACXC,aAAc,GACdC,eAAgB,EAChBC,kBAAmB,cACnBC,UAAW,eACXC,SAAU7J,KAAKkH,KAAKV,MACpBsD,QAASzF,EACT0F,UAAW,KACXC,WAAY,QACThK,KAAK8I,eAEVzI,EAAQ4J,UAAUC,UAAU1I,EAAS,CACnCU,aAAclC,KAAKkC,aACnBgF,KAAMlH,KAAKkH,KACXtF,SAAU5B,KAAK4B,SACfE,WAAY9B,KAAK8B,WACjBE,QAAShC,KAAKgC,QACduD,MAAOvF,KAAKuF,MACZqD,YAAa5I,KAAK4I,YAClBhD,OAAQ5F,OACPmK,MAAM9F,EACX,CACA,OAAOrE,KAAKuF,KACd,CACA6E,OACEpK,KAAK+I,WAAWqB,MAClB,CACAC,OACErK,KAAK+I,WAAWsB,MAClB,EAGFnK,EAAQkI,aAAeA,CAExB,EA1dA,CA0dGpI,KAAKC,GAAGwC,GAAKzC,KAAKC,GAAGwC,IAAM,CAAC,EAAGxC,GAAGA,GAAGqK,KAAKrK,GAAGsK,KAAKtK,GAAGwC,GAAGxC,GAAGA,GAAGuK,MAAMvK"}