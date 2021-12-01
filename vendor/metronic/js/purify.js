javascript: (function(doc) {
  function addListeners(){
    var add = window.addEventListener;
    add('mouseover',overHandler,false);
    add('mouseout',outHandler,false);
    add('click',clickHandler,false);
    add('keypress',keyHandler,false);
  }
  function removeListeners(){
    var rm = window.removeEventListener;
    rm('mouseover',overHandler,false);
    rm('mouseout',outHandler,false);
    rm('click',clickHandler,false);
    rm('keypress',keyHandler,false);
  }
  addListeners();
  var theElm;
  var theColor='orange';
  var flag=true;
  function overHandler(e){
    var elm = e.target;
    if(elm==doc.body||elm==doc.documentElement)return;
    theElm = elm;
    addBG(theElm);
  }
  function outHandler(e){
    removeBG(theElm);
  }
  function clickHandler(e){
    removeBG(theElm);
    if(flag){
      killOtherThan(theElm);
      flag=false;
      theColor='deepskyblue';
      theElm=null;
    }else kill(theElm);
    /*removeListeners();*/
    e.preventDefault();
  }
  function addBG(elm){
    var st = elm.style;
    if(st.backgroundColor)st.origBG = st.backgroundColor;
    st.backgroundColor = theColor;
  }
  function removeBG(elm){
    if(!elm)return;
    var st = elm.style;
    st.backgroundColor = st.origBG||'';
    delete st.origBG;
  }
  function keyHandler(e){
    try{Hash[e.keyCode||e.which](e)}catch(e){}
  }
  var Hash = {
    27:function(e){removeBG(theElm);if(flag){flag=false;theColor='deepskyblue';addBG(theElm)}else removeListeners()}/*Esc*/
    ,38:function(e){if(theElm.parentNode!=doc.body){removeBG(theElm);addBG(theElm=theElm.parentNode);}e.preventDefault()}/*up*/
    ,13:function(e){clickHandler(e)}/*Enter*/
    ,115:function(e){/*s*/
      removeBG(theElm);
      var html = doc.documentElement.cloneNode(true);
      var elms = [].slice.call(html.getElementsByTagName('*'));
      elms.forEach(function(e){
        var tag = e.tagName.toLowerCase();
        if(tag=='script'||tag=='meta'||e.display=='none'){e.parentNode.removeChild(e);}
        if(e.src)e.src=e.src;
        if(e.href)e.href=e.href;
      });
      var xml=(new XMLSerializer).serializeToString(html);
      location.href='data:application/octet-stream,'+encodeURIComponent(xml);
      removeBG(theElm);removeListeners();
    }
  };
  function killOtherThan(ele){
    while(ele!=doc.body){
      [].forEach.call(ele.parentNode.childNodes,function(e){if(e!=ele && e.style)e.style.display='none'});
      var st = ele.style;
      st.width='100%';
      st.padding=st.margin=0;
      ele=ele.parentNode;
    }
  }
  function kill(ele){
    if(!ele)return;
    ele.parentNode.removeChild(ele);
  }
})(document);


