// core ajax api
var core_data={},core_feedbacks={
	message:function(i){
		if (typeof(jQuery().toastmessage)=='function'){
			jQuery().toastmessage('showToast', {
				text     : i.message,
				sticky   : true,
				position : 'top-right',
			    type     : (i.type==2?'warning':(i.type==3?'success':'error'))
			});
		}else{
			alert(i.message);
		}
	},
	redirect:function(i){
		location.href=i.redirect?i.redirect:location.href;
	},
	template:function(i){
		jQuery(i.destination).html(Mustache.render(jQuery(i.template).html(),i.data) );
	},
	'var':function(i){
		core_data[i.name]=i.value;
	},
	console_log:function(i){
		switch (i.text_type){
			case 4:
                if (typeof (i.timer)!='undefined'){
                    setTimeout(function(){
                        console.info(i.data);
                    },parseInt(i.timer));
                }else{
                    console.info(i.data);
                }
	            break;
	        case 1:
                if (typeof (i.timer)!='undefined'){
                    setTimeout(function(){
                        console.warn('%c'+i.data,'color: red;');
                    },parseInt(i.timer));
                }else{
                    console.warn('%c'+i.data,'color: red;');
                }
				break;
	        case 2:
                if (typeof (i.timer)!='undefined'){
                    setTimeout(function(){
        	            console.warn(i.data);
                    },parseInt(i.timer));
                }else{
                    console.warn(i.data);
                }
	            break;
	        default: //3
                if (typeof (i.timer)!='undefined'){
                    setTimeout(function(){
        	            console.log(i.data);
                    },parseInt(i.timer));
                }else{
                    console.log(i.data);
                }
	    }
	},
	console_count:function(i){
		console.count(i.data);
	},
	console_dir:function(i){
        if (typeof (i.timer)!='undefined'){
            setTimeout(function(){
                console.dir(i.data);
            },parseInt(i.timer));
        }else{
            console.dir(i.data);
        }
	},
	console_table:function(i){
        if (typeof (i.timer)!='undefined'){
            setTimeout(function(){
                console.table(i.data);
            },parseInt(i.timer));
        }else{
            console.table(i.data);
        }
	},
	console_group:function(i){
        if (typeof (i.timer)!='undefined'){
            setTimeout(function(){
                switch (i.text_type){
	        case 1:
				if (i.collapsed){
					console.groupCollapsed('%c'+i.data,'color: red;');
				}else{
					console.group('%c'+i.data,'color: red;');
				}
		        break;
		    case 2:
		        if (i.collapsed){
					console.groupCollapsed('%c'+i.data,'color: orange;');
				}else{
					console.group('%c'+i.data,'color: orange;');
				}
		        break;
			default:
				if (i.collapsed){
					console.groupCollapsed(i.data);
				}else{
					console.group(i.data);
				}
		    }
            },parseInt(i.timer));
        }else{
            switch (i.text_type){
	        case 1:
				if (i.collapsed){
					console.groupCollapsed('%c'+i.data,'color: red;');
				}else{
					console.group('%c'+i.data,'color: red;');
				}
		        break;
		    case 2:
		        if (i.collapsed){
					console.groupCollapsed('%c'+i.data,'color: orange;');
				}else{
					console.group('%c'+i.data,'color: orange;');
				}
		        break;
			default:
				if (i.collapsed){
					console.groupCollapsed(i.data);
				}else{
					console.group(i.data);
				}
		    }
        }
	},
	console_groupEnd:function(i){
        if (typeof (i.timer)!='undefined'){
            setTimeout(function(){
                console.groupEnd();
            },parseInt(i.timer));
        }else{
            console.groupEnd();
        }
	}
};
function core_ajax(act,array,object,loading,success_callback){
	array=array||{};
//	array['_ajax']=true;
	if (typeof(array)=='string'){
            array+='&_act='+act;
        }else{
            array['_act']=act;
        }
	if (loading)jQuery(object).addClass('loading');
	jQuery.post(location.href,array).success(function(input){
		try{
			var data=JSON.parse(input);
		}
		catch(e){
			alert('Ajax format error. No actions');
			return;
		}
		for (var key in data){
			var item=data[key];
			if (typeof(item)=='undefined')continue;
			if (typeof(core_feedbacks[item._type])!='undefined'){
				core_feedbacks[item._type](item,object);
			}else{
				console.log('Action "'+item._type+'" not registered in system');
			}
		}
		if (success_callback)success_callback();
	}).error(function(xjr,text,status){
		console.log(xjr);	
		alert('Request error '+text+' '+status);
		return;
	}).done(function(){
		if (loading)jQuery(object).removeClass('loading');
	});
}
/**
 * 
 * @param {type} selector
 * @param {type} act
 * @param {type} array
 * @param {type} object
 * @param {type} loading
 * @param {type} success_callback
 * @returns {undefined}
 */
function core_load(selector,act,array,object,loading,success_callback){
	array=array||{};
    if (typeof(array)=='string'){
		if (act)array+='&_act='+act;
		array+='&_partition_content=true';
	}else{
		if (act)array['_act']=act;
		array['_partition_content']=true;
	}
        //$(selector).load(location.href,array,callback);
	if (loading)jQuery(object).addClass('loading');
	jQuery.post(location.href,array).success(function(input){
		try{
			var data=JSON.parse(input);
			if (typeof(data)=='number')return jQuery(selector).html(input);
			console.log(typeof(data));
		}catch(e){
			jQuery(selector).html(input);
			if (success_callback)success_callback();
			return ;
		}
		for (var key in data){
			var item=data[key];
			if (typeof(item)=='undefined')continue;
			if (typeof(core_feedbacks[item._type])!='undefined'){
				core_feedbacks[item._type](item,object);
			}else{
				console.log('Action "'+item._type+'" not registered in system');
			}
		}
		if (success_callback)success_callback();
	}).error(function(xjr,text,status){
		alert('Request error '+text+' '+status);
		return;
	}).done(function(){
		if (loading)jQuery(object).removeClass('loading');
	});
}


/* sample of user functions
// do action
function vote_battle1(){
	core_ajax('vote',{battle: 1});
}

//register callback
core_feedbacks['vote1']=function(item){
	jQuery('#vote-btn-1').removeClass('btn-danger').addClass('btn-muted').attr('title',item.title);
	jQuery('#vote-btn-1').tooltip({placement: 'right'})
}
 */