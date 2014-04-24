function DoAsciiHex(x,dir)
{hex="0123456789ABCDEF";almostAscii=' !"#$%&'+"'"+'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ['+'\\'+']^_`abcdefghijklmnopqrstuvwxyz{|}';r="";
if(dir=="A2H")
{for(i=0;i<x.length;i++){let=x.charAt(i);pos=almostAscii.indexOf(let)+32;h16=Math.floor(pos/16);h1=pos%16;r+=hex.charAt(h16)+hex.charAt(h1);};};
if(dir=="H2A")
{for(i=0;i<x.length;i++){let1=x.charAt(2*i);let2=x.charAt(2*i+1);val=hex.indexOf(let1)*16+hex.indexOf(let2);r+=almostAscii.charAt(val-32);};};
return r;
};

Array.prototype.unique =
	function() {
	var a = [];
	var l = this.length;
	for(var i=0; i<l; i++) {
	  for(var j=i+1; j<l; j++) {
		// If this[i] is found later in the array
		if (this[i] === this[j])
		  j = ++i;
	  }
	  a.push(this[i]);
	}
	return a;
};

$.fn.focusNextInputField = function() {
    return this.each(function() {
        var fields = $(this).parents('form:eq(0),body').find('button,input,textarea,select');
        var index = fields.index( this );
        if ( index > -1 && ( index + 1 ) < fields.length ) {
            fields.eq( index + 1 ).focus();
        }
        return false;
    });
};