/**
 * $Id: editor_plugin_src.js 1029 2009-02-24 22:32:21Z spocke $
 *
 * @author Moxiecode
 * @copyright Copyright � 2004-2008, Moxiecode Systems AB, All rights reserved.
 
 * Modified fullpage plugin by dave.
 */

(function() {
	tinymce.create('tinymce.plugins.HalfPagePlugin', {
		init : function(ed, url) {
			var t = this;

			t.editor = ed;

			

			ed.onBeforeSetContent.add(t._setContent, t);
			ed.onSetContent.add(t._setBodyAttribs, t);
			ed.onGetContent.add(t._getContent, t);
		},

		getInfo : function() {
			return {
				longname : 'Halfpage',
				author : 'Moxiecode Systems AB & dtbaker.com.au',
				authorurl : 'http://dtbaker.com.au',
				infourl : 'http://dtbaker.com.au/random-bits/tinymce-halfpage-plugin.html',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		},

		// Private plugin internal methods

		_setBodyAttribs : function(ed, o) {
			var bdattr, i, len, kv, k, v, t, attr = this.head.match(/body(.*?)>/i);

			if (attr && attr[1]) {
				bdattr = attr[1].match(/\s*(\w+\s*=\s*".*?"|\w+\s*=\s*'.*?'|\w+\s*=\s*\w+|\w+)\s*/g);

				if (bdattr) {
					for(i = 0, len = bdattr.length; i < len; i++) {
						kv = bdattr[i].split('=');
						k = kv[0].replace(/\s/,'');
						v = kv[1];

						if (v) {
							v = v.replace(/^\s+/,'').replace(/\s+$/,'');
							t = v.match(/^["'](.*)["']$/);

							if (t)
								v = t[1];
						} else
							v = k;

						ed.dom.setAttrib(ed.getBody(), 'style', v);
					}
				}
			}
		},

		_createSerializer : function() {
			return new tinymce.dom.Serializer({
				dom : this.editor.dom,
				apply_source_formatting : true
			});
		},

		_setContent : function(ed, o) {
			var t = this, sp, ep, c = o.content, v, st = '';

			if (o.source_view && ed.getParam('halfpage_hide_in_source_view'))
				return;

			// Parse out head, body and footer
			c = c.replace(/<(\/?)BODY/gi, '<$1body');
			sp = c.indexOf('<body');

			
			if (sp != -1) {
				sp = c.indexOf('>', sp);
				t.head = c.substring(0, sp + 1);

				ep = c.indexOf('</body', sp);
				if (ep == -1)
					ep = c.indexOf('</body', ep);

				o.content = c.substring(sp + 1, ep);
				t.foot = c.substring(ep);

				function low(s) {
					return s.replace(/<\/?[A-Z]+/g, function(a) {
						return a.toLowerCase();
					})
				};

				t.head = low(t.head);
				t.foot = low(t.foot);
			} else {
				// this is really the only modification I made, removing all the code
				// that added the default body stuff if it didnt exist.
				t.head = '';
				t.foot = '';
			}
		},

		_getContent : function(ed, o) {
			var t = this;

			if (!o.source_view || !ed.getParam('halfpage_hide_in_source_view'))
				o.content = tinymce.trim(t.head) + '\n' + tinymce.trim(o.content) + '\n' + tinymce.trim(t.foot);
		}
	});

	// Register plugin
	tinymce.PluginManager.add('halfpage', tinymce.plugins.HalfPagePlugin);
})();