//Timer for messages and tasks

//Tooltip
$('a').tooltip('hide');

//Popover
$('.popover-pop').popover('hide');

//Collapse
$('#myCollapsible').collapse({
  toggle: false
})

//Dropdown
$('.dropdown-toggle').dropdown();


// Retina Mode
function retina(){
  retinaMode = (window.devicePixelRatio > 1);
  return retinaMode;
}


