/* $Id: jquery.jeditable.js,v 1.16 2006/10/10 18:55:34 tuupola Exp $ */
/**
  * jQuery inplace editor plugin.  
  *
  * Based on editable by Dylan Verheul <dylan@dyve.net>
  * http://www.dyve.net/jquery/?editable
  *
  *
  * Test and added new possibilities by Ivan Pospekhov <ipo@irk.ru>
  * options[type]  =  select|file|radio
  * http://www.ipo-design.ru/developments/lab/jEditable
  *
  *
  * @name  jEditable
  * @type  jQuery
  * @param String  url                POST URL to send edited content
  * @param Hash    options            additional options 
  * @param String  options[name]      POST parameter name of edited content
  * @param String  options[id]        POST parameter name of edited div id
  * @param String  options[type]      text or textarea
  * @param Integer options[rows]      number of rows if using textarea
  * @param Integer options[cols]      number of columns if using textarea
  * @param Mixed   options[height]    'auto' or height in pixels
  * @param Mixed   options[width]     'auto' or width in pixels 
  * @param String  options[postload]  POST URL to fetch content before editing
  * @param String  options[getload]   GET URL to fetch content before editing
  * @param String  options[xmlload]   POST URL to fetch content before editing in SELECT
  * @param String  options[indicator] indicator html to show when saving
  * @param String  options[tooltip]   optional tooltip text via title attribute
  * @param String  options[event]     jQuery event such as 'click' of 'dblclick'
  *             
  */

jQuery.fn.editable = function(url, options) {
//	alert('!!!');
    var settings = {
        url    : url,
        name   : 'value',
        id     : 'id',
        type   : 'text',
        width  : 'auto',
        height : 'auto',
        event  : 'click'
    };

    if(options) {
        jQuery.extend(settings, options);
    };

    jQuery(this).attr("title", settings.tooltip);
    jQuery(this)[settings.event](function(e) {

        /* save this to self because this changes when scope changes */
        var self = this;

        /* prevent throwing an exeption if edit field is clicked again */
        if (self.editing) {
            return;
        }

        /* figure out how wide and tall we are */
        settings.width = 
            ('auto' == settings.width)  ? jQuery(self).width()  : settings.width;
        settings.height = 
            ('auto' == settings.height) ? jQuery(self).height() : settings.height;

        self.editing    = true;
        self.revert     = jQuery(self).html();
        self.innerHTML  = "";

        /* create the form object */
        var f = document.createElement("form");

        // insert in FORM object for send type='file'
//       f.setAttribute('enctype','multipart/form-data');
//	 f.enctype =  'multipart/form-data'; 
         f.enctype = "application/x-www-form-urlencoded";


        /*  main input element */
        var i;

     // insert tag SELECT
     /************************************/
     if ("select" == settings.type) {

        i = document.createElement("select");
        i.name  = settings.name;
//  		i.name  = escape(settings.name).replace(new RegExp('\\+','g'), '%2B');

         /*  method POST value list and names */
        var s = {};
        s[settings.id] = self.id;

	// is necessary indicate source xml ! otherwise sense no use 'select' ;) 
	 if (settings.xmlload) {

            jQuery.post(settings.xmlload, s, function(xml) {		
//		alert('222');
			
             	var nodes = xml.getElementsByTagName('item');
		var o = 0;
		for(o=0; o<nodes.length; o++) {
		var node = nodes[o];
		oOption1 = document.createElement("OPTION");

		if(node.getElementsByTagName('curent')[0].firstChild.nodeValue == 1)
		oOption1.selected = 'true';

		oOption1.value = node.getElementsByTagName('value')[0].firstChild.nodeValue;								
                oOption1.appendChild(document.createTextNode(node.getElementsByTagName('name')[0].firstChild.nodeValue));
		i.appendChild(oOption1);							

		}
            }); 
        }

//        i.value = self.revert;
          i.setAttribute('autocomplete','off');

     } else {

     /************************************/

	if ("textarea" == settings.type) {
            i = document.createElement("textarea");
            if (settings.rows) {
                i.rows = settings.rows;
            } else {
                jQuery(i).height(settings.height);
            }
            if (settings.cols) {
                i.cols = settings.cols;
            } else {
                jQuery(i).width(settings.width);
            }

        } else {

	// by default  type : text, try in constructor to indicate type   :  'radio'  или  type   :  'file'
	// ;-)

			i = document.createElement("input");
			i.type  = settings.type;
            /* https://bugzilla.mozilla.org/show_bug.cgi?id=236791 */
            i.setAttribute('autocomplete','off');
        }

		i.name  = settings.name;
//  		i.name  = escape(settings.name).replace(new RegExp('\\+','g'), '%2B');
         /* fetch input content via POST or GET */
        var l = {};
        l[settings.id] = self.id;
         if (settings.getload) {
            jQuery.get(settings.getload, l, function(str) {
                i.value = str;
            });
        } else if (settings.postload) {
            jQuery.post(settings.postload, l, function(str) {
                i.value = str;
            }); 
        } else { 
            i.value = self.revert;
        }
    }
//  		i.name  = escape(settings.name).replace(new RegExp('\\+','g'), '%2B');
        f.appendChild(i);

        // button 'OK' for all, except  type :  text
        if ("text" != settings.type) {
            var b = document.createElement("input");
            b.type = "submit";
            b.value = "OK";
            f.appendChild(b);
        }


        /* add created form to self */
        self.appendChild(f);

        i.focus();
 
        /* discard changes if pressing esc */
        jQuery(i).keydown(function(e) {
	    if (e.keyCode == 27) {
                e.preventDefault();
                reset();
            }
        });

        /* discard changes if clicking outside of editable */

        var t;

        jQuery(i).blur(function(e) {
            t = setTimeout(reset, 200)
        });


        jQuery(f).submit(function(e) {

            if (t) { 
                clearTimeout(t);
            }

            /* do no submit */
            e.preventDefault(); 

            /* add edited content and id of edited element to POST */           
            var p = {};


//            p[i.name] = escape(jQuery(i).val());
			p[i.name] = jQuery(i).val();
            p[settings.id] = self.id;

            /* show the saving indicator */
            jQuery(self).html(options.indicator);
            jQuery(self).load(settings.url, p, function(str){		
			if(str.indexOf('<!--updateNow')>=0)
				{
				text_arr = str.split('_');
//				alert(str);
				updateRight(text_arr[1]);
				}
            self.editing = false;
            });

	// loading any information from external source
	/****************************************************/
		if (settings.extload) 
			{
				extLoad = settings.extload.split(',');
				container = settings.container.split(',');
				var extCnt = extLoad.length;
				var contCnt = container.length;
				alert(extCnt + '  -  ' + contCnt);
				for(i=0; i<contCnt; i++ )	
					{
					//alert(extLoad[i] + '  -  ' + container[i]);					
					reload(extLoad[i], container[i]);
					}
	        }
	/****************************************************/
        });


        function reset() {
            self.innerHTML = self.revert;
            self.editing   = false;

        };


	/****************************************************/
	//   Loading external information (text or html or... ) 
        //   installation in element <div id="result"> </div>

        function reload(extLoad, container) {
           var p = {};
//            s[i.name] = jQuery(i).val();
            s[i.name] = escape(jQuery(i).val());
            s[settings.id] = self.id;
            jQuery.post(extLoad, s, function(str) {
            document.getElementById(container).innerHTML = str ;
         }); 
	/****************************************************/



        };


    });

    return(this);
}

