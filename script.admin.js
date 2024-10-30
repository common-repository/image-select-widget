function getjQueryImageSelectUpdateId() 	{ return jQuery("#image_select_update"); 		}

function getjQueryImageSelectPreviewClass()	{ return jQuery(".image_select_preview");		}
function getjQueryImageSelectSpanClass() 	{ return jQuery(".image_select_span");			}
function getjQueryImageSelectWrapClass() 	{ return jQuery(".image_select_wrap");			}
function getjQueryImageSelectEditClass() 	{ return jQuery(".image_select_wrap .edit");	}
function getjQueryImageSelectMoreClass() 	{ return jQuery(".more");						}

function getImageSelectId() {				return document.getElementById("image_select");					}
function getImageSelectNewId() {			return document.getElementById("image_select_new");				}
function getImageSelectWrapId(id) {			return document.getElementById("image_select_wrap_" + id);		}
function getImageSelectLabelId(id) {		return document.getElementById("image_select_label_" + id);		}
function getImageSelectThemeId(id) {		return document.getElementById("image_select_theme_" + id);		}
function getImageSelectInfoId() {			return document.getElementById("image_select_info");			}
function getImageSelectUpdateId() {			return document.getElementById("image_select_update");			}
function getImageSelectCountNewId() {		return document.getElementById("image_select_count_new");		}
function getImageSelectCountImgId() {		return document.getElementById("image_select_count_img");		}
function getImageSelectUpdateContentId() {	return document.getElementById("image_select_update_content");	}
function getImageSelectHelperId(id) {		return document.getElementById("image_select_helper_" + id);	}
function getImageSelectThemeNewId() {		return document.getElementById("image_select_theme_new");		}
function getImageSelectNewInputId() {		return document.getElementById("image_select_theme_new_input");	}

function getImageSelectDeleteClass() {		return document.getElementsByClassName("image_select_delete");	}


jQuery(window).load(function(){
	imageSelectMoreImages();
});

jQuery(window).resize(function() {
	imageSelectMoreImages();
});

function imageSelectHide() {
	var theme_update = getjQueryImageSelectUpdateId();
	var n = getImageSelectCountNewId();
	var i = getImageSelectCountImgId();
	
	theme_update.hide();
	n.value = i.value = 0;
	document.theme.ak.value = "";
}

function imageSelectAddImage(label) {
	var t = getImageSelectUpdateId();
	
	if(label) label.parentNode.parentNode.removeChild(label.parentNode);
	
	cont = getFirstChild(getFirstChild(t));
	
	createNewImageInput();	
}

function createNewImageInput() {
	var i = getImageSelectCountNewId();
	var t = getImageSelectUpdateContentId();
	i.value = parseInt(i.value) + 1;
	
	var input = document.createElement('input'); 
	
		input.className = "image_select_helper_input";
		input.type = "text";
		input.name = "new_image_" + i.value;
		input.value = info;
		input.onclick = function() { this.select() };
	
	t.appendChild(input);
	
	var label = document.createElement('label');
		
		label.id = "image_select_theme_new_image";
		label.for = "new_image_" + i.value;
		
	t.appendChild(label);
	
	var span = document.createElement('span');
	
		span.className = "button button-primary";
		span.title = "Add";
		span.onclick = function() { imageSelectAddImage(this) };
		span.innerHTML = '+';
		
	label.appendChild(span);
}

function imageSelectUpdate(id, name) {
	var t = getImageSelectUpdateId();
	var e = document.getElementById(name + id);
	var s = getImageSelectId();
	var h = getImageSelectHelperId(id);
	var i = getImageSelectCountImgId();
	
	var urls = h.innerHTML.split(';');
	var count = 0;
	
	var p = document.getElementsByClassName('image_select_preview')[0];
	
	cont = getFirstChild(getFirstChild(t));
	
	cont.innerHTML = "";
		
	for(; count < urls.length - 1; count++) {
		if(urls[count] == '')
			urls[count] = info;
			
		cont.innerHTML += '<input id="image_' + (count + 1) + '" class="image_select_helper_input" type="text" name="image_' + (count + 1) + '" value="' + urls[count] + '" />';
		cont.innerHTML += '<span class="image_select_delete" onclick="javascript:void(imageSelectDeleteImg(\'' + (count + 1) + '\', \'' + id +'\'))" title="delete"></span>';
		cont.innerHTML += '<label for="image_' + (count + 1) + '"><img src="' + urls[count] + '" /></label>';
	}
	
	i.value = count;

	createNewImageInput();
	
	document.theme.ak.value = "images";
	document.theme.theme.value = id;
	
	t.style.top 	=  s.offsetTop 		- 5		+ 'px';
	t.style.left	=  s.offsetLeft 	- 5		+ 'px';
	t.style.width	= (s.offsetWidth 	- 10) 	+ 'px';
	t.style.height  = (s.offsetHeight 	- 10)	+ 'px';
	
	t.style.display = 'block';
		
	t.focus();
	
	return true;
}

function imageSelectCategory(id) {
	var t = getImageSelectThemeNewId();
	
	if(t.style.display == 'block')
		imageSelectRejectCategory();
		
	document.theme.id.value = id;
		
	var s = getImageSelectId();
	var i = getImageSelectNewInputId();
	var d = getImageSelectDeleteClass()[0];
	
	t.style.left  = s.offsetLeft + 'px';
	t.style.width = (s.offsetWidth - 10 ) + 'px';
	
	if(id != 0) {
		d.style.display = 'block';
		
		var w = getImageSelectWrapId(id);
		var l = getImageSelectLabelId(id);
		
		t.style.top = (w.offsetTop + 5 ) + 'px';
		i.value = l.innerHTML;
	} else {
		d.style.display = 'none';
		
		var n = getImageSelectNewId();
		t.style.top = (n.offsetTop - 1) + 'px';
	}
	
	t.style.display = 'block';
	
	//i.focus();
	i.select();
}

function imageSelectRejectCategory() {
	var t = getImageSelectThemeNewId();
	var i = getImageSelectNewInputId();
	
	i.value = "";	
	t.style.display = 'none';
}

function imageSelectDeleteTheme(id) {
	var l = getImageSelectThemeId(id);
	
	if(!confirm(delImg.replace(/%s/g, l.innerHTML)))
		return false;
		
	document.select.id.value = id;
	document.select.submit();
	
	return true;
}

function imageSelectDeleteImg(id, theme) {
	var title = 'image_' + id;
	var l = document.getElementById(title)
	
	if(l.title)
		title = l.title;
	else if(l.alt)
		title = l.alt;
	
	if(!confirm(delImg.replace(/%s/g, title)))
		return false;
		
	document.theme.ak.value = 'deleteImg';
	document.theme.id.value = id;
	document.theme.theme.value = theme;
	document.theme.submit();
	
	return true;
}

function imageSelectDeleteCategory() {
	var id = document.theme.id.value;
	var l = getImageSelectLabelId(id);
	
	if(!confirm(delThm.replace(/%s/g, l.innerHTML)))
		return false;
	
	document.theme.id.value = id;
	document.theme.ak.value = "delete";
	document.theme.submit();
	
	return true;
}

function imageSelectMoreImages() {
	var preview 	= getjQueryImageSelectPreviewClass();
	
	if(!preview) return;
	
	var selectSpan 	= getjQueryImageSelectSpanClass();
	var wrap		= getjQueryImageSelectWrapClass();
	var edit 		= getjQueryImageSelectEditClass();
	var clean		= getjQueryImageSelectMoreClass();
	
	var spanMaxLength = 0;
	var editMaxLength = 0;
	var wrapMaxLength = 0;
	var spanLength = 0;
	
	for(i = 0; i < clean.length; i++)
		clean[i].parentNode.removeChild(clean[i]);
	
	for(i = 0; i < selectSpan.length; i++)
		if(selectSpan[i].offsetWidth > spanMaxLength)
			spanMaxLength = selectSpan[i].offsetWidth;
	
	for(i = 0; i < edit.length; i++)
		if(edit[i].offsetWidth > editMaxLength)
			editMaxLength = edit[i].offsetWidth;
			
	for(i = 0; i < wrap.length; i++)
		if(wrap[i].offsetWidth > wrapMaxLength)
			wrapMaxLength = wrap[i].offsetWidth;
			
	spanLength = wrapMaxLength - spanMaxLength - editMaxLength - 8; // padding
	
	for(i = 0; i < preview.length; i++) {
		var span = getFirstChild(preview[i]);
		
		span.style.width 		=  spanLength + "px";
		span.style.marginLeft	= -spanLength + "px";
		
		var imgsLength = 0;
		var maxLength  = 0;
		
		var img = preview[i].getElementsByTagName('img');
		var index = img[0];
		
		var more = document.createElement('div');
		var moreWidth 		= 28;
		var moreMarginRight = 5;
		var moreBorder		= 2;
		
		var hidden  = 0;
		
		for(j = 0; j <= img.length; j++) {
			if(img[j]) {
				imgsLength += jQuery(img[j]).outerWidth(true);
				
				if(imgsLength <= spanLength) {
					maxLength = imgsLength;
					index = img[j];
				} else
					hidden++;
			}
		}
		
		if(hidden > 0) {
			if(maxLength + moreWidth + moreMarginRight + moreBorder < spanLength)
				index = index.nextSibling;
			else
				hidden++;
			
			if(index) {
				preview[i].firstChild.insertBefore(more, index);
				more.className 			= 'more';
				more.style.width 		= moreWidth + 'px';
				more.style.marginRight 	= moreMarginRight + 'px';
				more.innerHTML 			= '+' + hidden;
			}
		}
	}
}

function imageSelectInfo() {
	var t = getImageSelectInfoId();
	var s = getImageSelectId();
	
	if(t.style.display != 'block') {
		t.style.top 	=  s.offsetTop 		- 5		+ 'px';
		t.style.left	=  s.offsetLeft 	- 5		+ 'px';
		t.style.width	= (s.offsetWidth 	- 10) 	+ 'px';
		t.style.height  = (s.offsetHeight 	- 10)	+ 'px';
		
		t.style.display = 'block';
	} else
		t.style.display = 'none';
}

function getFirstChild(el){
	var firstChild = el.firstChild;
	while(firstChild != null && firstChild.nodeType == 3) {
		firstChild = firstChild.nextSibling;
	}
	return firstChild;
}