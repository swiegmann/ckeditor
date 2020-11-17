jQuery(document).ready(function () {
  jQuery('textarea[class*="ckeditor"]').each(function(index) {
    var el = this;

    var classNames = this.className,
      myClassName = '',
      a = classNames.split(' ');

    for (var i in a){
      if (a[i].toString().indexOf('ckeditor') != -1) {
        myClassName = a[i];
      }
    }

    // Set the configurationdata:
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


    // Handling special characters, such as nbsp's (Part I)
    var fnCKEditorCleanup = function(s) {
      s = s.replace(/&nbsp;{1,}/g, " ");
      s = s.replace(/\s{2,}/g, " ");

      return s;
    }

    el.value = fnCKEditorCleanup(el.value);


    // CreateEditor
    ClassicEditor.create(this, ckCfg);


    // Handling special characters, such as nbsp's: (Part II)
    document.querySelector('form[role="form"]').addEventListener("submit", function() {
      el.value = fnCKEditorCleanup(el.value)
    });
  });
});
