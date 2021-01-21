var binds=binds || {};
var uniqueBinds={};

function redraw(){
    const value=document.getElementById('textarea').value;
    const regex = /:\w+/gm;

    var bindHtml=''
    uniqueBinds={};
while ((m = regex.exec(value)) !== null) {
    if (m.index === regex.lastIndex)regex.lastIndex++;
    m.forEach((match) => {
        var bind=match.substr(1);
        uniqueBinds[bind]=true;
    });
}
for (var bind in uniqueBinds){
    bindHtml+='<label>'+bind+': <input name="binds['+bind+']" value="'+(typeof binds[bind] === 'undefined' ? '' :binds[bind]) +'" oninput="binds[\''+bind+'\']=this.value"></label>';
}

document.getElementById('binds').innerHTML=bindHtml;

}

redraw();