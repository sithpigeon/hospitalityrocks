/**
  *Author: David Boggus
  *URL: www.boggusweb.com
  *Date: 9-15-06
  **/
var dbMenu = {
    init: function(){
        var uls = document.getElementsByTagName('ul');
        for(var i = 0; i < uls.length; i++){
            if(uls[i].className.search(/\bdbMenu\b/) == -1)
                continue;
            var menu = uls[i];
            
            dbMenu.styleSubMenus(menu);
            
            addEvent(menu, 'mouseover', dbMenu.hover, false);
            addEvent(menu, 'mouseout', dbMenu.hoverOff, false);
            if(menu.className.search(/\bonMouse\b/) == -1){
                addEvent(menu, 'click', dbMenu.click, false);
            }
            addEvent(menu, 'click', dbMenu.nav, false);
        }    
    },
    
    hover: function(e){
        var target = (window.event)? window.event.srcElement : (e)? e.target : null;
        
        if(target){
            target = dbMenu.getTarget(target, 'li');
            if(!target) return;
        }else{
            return;
        }
        
        target.className += ' hover';
        
        var t = (target.className.search(/\bsubMenu\b/) != -1)? target : (target.parentSubMenu)? target.parentSubMenu : null;
        if(!t) return;
        clearTimeout(t.timeout);
        
        if(target.parentMenu.className.search(/\bonMouse\b/) != -1){
            t.className += ' click';
        }
    },
    
    hoverOff: function(e){
        var target = (window.event)? window.event.srcElement : (e)? e.target : null;
        
        if(target){
            target = dbMenu.getTarget(target, 'li');
            if(!target) return;
        }else{
            return;
        }
        
        target.className = target.className.replace(/hover/g, '');
        
        if(target.parentMenu.className.search(/\bonMouse\b/) != -1){
            var t = (target.className.search(/\bsubMenu\b/) != -1)? target : (target.parentSubMenu)? target.parentSubMenu: null;
            if(!t) return; 
            t.timeout = setTimeout(function(){ t.className = t.className.replace(/click/g, ''); }, 30);
        }
    },
    
    click: function(e){
        if(window.event){
            window.event.cancelBubble = true;
        }
        if(e && e.stopPropagation){
            e.stopPropagation();
        }
        var target = (window.event)? window.event.srcElement : (e)? e.target : null;
        
        if(target){
            target = dbMenu.getTarget(target, 'li');
            if(!target) return;
        }else{
            return;
        }
        
        if(target.className.search(/\bclick\b/) == -1){
            target.className += ' click';
        }else{
            target.className = target.className.replace(/click/g, '');
        }
    },
    
    nav: function(e){
        if(window.event){
            window.event.cancelBubble = true;
        }
        if(e && e.stopPropagation){
            e.stopPropagation();
        }
        var target = (window.event)? window.event.srcElement : (e)? e.target : null;
        
        if(target){
            target = dbMenu.getTarget(target, 'li');
            if(!target) return;
        }else{
            return;
        }
        
        for(var i = 0; i < target.childNodes.length; i++){
            var node = target.childNodes[i];
            if(node.nodeName.toLowerCase() == 'a'){
                window.location = node.href;
                break;
            }
        }
    },
    
    getTarget: function(target, elm){
        if(target.nodeName.toLowerCase() != elm && target.nodeName.toLowerCase() != 'body'){
            return dbMenu.getTarget(target.parentNode, elm);
        }else if(target.nodeName.toLowerCase() == 'body'){
            return null;
        }else{
            return target;
        }
    },
    
    styleSubMenus: function(menu){
        lis = menu.getElementsByTagName('li');
        for(var i = 0; i < lis.length; i++){
            node = lis[i];
            node.parentMenu = menu;
            if(node.getElementsByTagName('ul').length != 0){
                node.className += ' subMenu';
                sublis = node.getElementsByTagName('li');
                for(var j = 0; j < sublis.length; j++){
                    sublis[j].parentSubMenu = node;
                }
            }
        }
    }
}
    
function addEvent(elm, evType, fn, useCapture){  //cross-browser event handling for IE5+, NS6+, and Mozilla/Gecko By Scott Andrew
	if(elm.addEventListener){
		elm.addEventListener(evType, fn, useCapture);
		return true;
	}else if(elm.attachEvent){
		var r = elm.attachEvent('on' + evType, fn);
		return r;
	}else{
		elm['on' + evType] = fn;
	}
}

addEvent(window, 'load', dbMenu.init, false);