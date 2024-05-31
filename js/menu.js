// JavaScript Document
function vertical() {
   var navItems = document.getElementById("nav").getElementsByTagName("li");
    
   for (var i=0; i< navItems.length; i++) {
      if (navItems[i].className == "submenu") {
         navItems[i].onmouseover=function() {this.getElementsByTagName('ul')[0].style.display="block";}
         navItems[i].onmouseout=function() {this.getElementsByTagName('ul')[0].style.display="none";}
      }
   }

}

function horizontal() {
   var navItems = document.getElementById("menu_principal").getElementsByTagName("li");
    
   for (var i=0; i< navItems.length; i++) {
      if ((navItems[i].className == "menu_vertical") || (navItems[i].className == "submenu")) {
         if (navItems[i].getElementsByTagName('ul')[0] != null) {
            navItems[i].onmouseover=function() {this.getElementsByTagName('ul')[0].style.display="block";}
            navItems[i].onmouseout=function() {this.getElementsByTagName('ul')[0].style.display="none";}
         }
      }
   }

}