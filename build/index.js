(()=>{var e=wp.blocks.registerBlockType,t=wp.editor,a=t.RichText,n=t.MediaUpload,r=t.BlockControls,l=wp.components.Button;e("mon-plugin/gutenberg-block",{title:"Mon Bloc Image",description:"Un bloc personnalisé qui permet de télécharger une image.",category:"common",icon:"format-image",attributes:{imageURL:{type:"string",default:""},imageAlt:{type:"string",default:""}},edit:function(e){var t=e.attributes,i=e.setAttributes,c=t.imageURL,o=t.imageAlt;return React.createElement("div",{className:"mon-plugin-gutenberg-block"},React.createElement(r,null,React.createElement(n,{onSelect:function(e){return i({imageURL:e.url,imageAlt:e.alt})},allowedTypes:["image"],value:c,render:function(e){var t=e.open;return React.createElement(l,{onClick:t},"Choisir une image")}})),c&&React.createElement("img",{src:c,alt:o,style:{width:"100%"}}),React.createElement(a,{tagName:"p",placeholder:"Ajouter une description...",value:o,onChange:function(e){return i({imageAlt:e})}}))},save:function(e){var t=e.attributes,n=t.imageURL,r=t.imageAlt;return React.createElement("div",{className:"mon-plugin-gutenberg-block"},n&&React.createElement("img",{src:n,alt:r,style:{width:"100%"}}),React.createElement(a.Content,{tagName:"p",value:r}))}})})();