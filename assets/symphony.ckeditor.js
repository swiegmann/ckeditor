jQuery(document).ready(function () {
  jQuery('textarea[class*="ckeditor"]').each(function(index) {
    var el = this,
      classNames = this.className,
      myClassName = '',
      a = classNames.split(' ');

    for (var i in a) {
      if (a[i].toString().indexOf('ckeditor') != -1) {
        myClassName = a[i];
      }
    }


    // Set CKEditor-Configuration:
    var ckCfg = {};
    ckCfg.language = Symphony.Context.get('lang');
  	// ckCfg.allowedContent = 'h3; p; blockquote; ul; ol; li; a[href]; sup; strong; em; u; strike';
    // ckCfg.extraAllowedContent = 'section-img,section-gallery';
    // ckCfg.forcePasteAsPlainText = true;
    // ckCfg.format_tags = 'h3;p';
    // ckCfg.filebrowserBrowseUrl = Symphony.Context.get('root') + '/symphony/extension/ckeditor/filebrowser/';
		ckCfg.filebrowserBrowseUrl = '';
		ckCfg.filebrowserImageBrowseUrl = '';
		ckCfg.filebrowserFlashBrowseUrl = '';


    // Set the correct preset:
    for(var i in ckeditor_presets) {
      if(ckeditor_presets[i].class == myClassName) {
        var info = jQuery.extend(true, {}, ckeditor_presets[i]);
        ckCfg.toolbar = info.toolbar[0];
      }
    }


    //Stop CKEditor creating another instance
    jQuery(this).removeClass(myClassName);



    var fnCKEditorCleanup = function(s) {
      s = s.replace(/&nbsp;{1,}/g, " ");
      s = s.replace(/\s{2,}/g, " ");

      return s;
    }

    function fnNthIndexOf(str, pat, n) { // https://stackoverflow.com/a/14482123
      var L = str.length,
        i = -1;
      while (n-- && i++<L) {
        i = str.indexOf(pat, i);
        if (i < 0) break;
      }
      return i;
    }


    // Handling special characters, such as nbsp's (Part I)
    el.value = fnCKEditorCleanup(el.value);


    // CreateEditor
    ClassicEditor.create(el, ckCfg);


    // On submitting form, convert & clean the field-value
    // Handling special characters, such as nbsp's: (Part II)
    document.querySelector('form[role="form"]').addEventListener("submit", function(e) {
      // HTML > XML (https://stackoverflow.com/a/54078281)
      const node = document.createElement('div');

      node.innerHTML = el.value;
      let xmlData = new XMLSerializer().serializeToString(node);
      xmlData = xmlData.substr(fnNthIndexOf(xmlData, "<", 2)); // Remove node-tag (open)
      xmlData = xmlData.substr(0, xmlData.lastIndexOf("<")); // Remove node-tag (close)
      xmlData = fnCKEditorCleanup(xmlData); // Cleanup
      el.value = xmlData;
      el.innerHTML = xmlData;
    });
  });
});
