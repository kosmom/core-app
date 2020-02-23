function waitSecAndCallAlert(){
	core_ajax('waitSecAndCallAlert');
}
function waitSecAndCallAlertWithClientCallback(){
	core_ajax('waitSecAndCallAlert',{},null,false,function(){
		alert('client side callback');
	});
}
function waitSecAndCallAlertWithLoadingState(object){
	core_ajax('waitSecAndCallAlert',{},object,true);
}

function callCustomCallback(){
	core_ajax('callCustomCallback');
}

function redirectToMain(){
	core_ajax('redirectToMain');
}
function callWithClientData(){
	let someString = prompt('Give some string','some string');
	core_ajax('callWithClientData',{ someString });
}

function loadContent(){
	core_load('#content','loadContent');
}

core_feedbacks['some_callback']=function(data){
  let div = document.createElement('div');
  div.className = "alert";
  div.innerHTML = "<strong>Added with costome callback</strong>"+data;

  document.body.append(div);
}