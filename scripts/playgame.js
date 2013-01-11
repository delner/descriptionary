function loadDrawingFlashObj()
{
	var flashvars = { id:4 };
	var params = {};
	var attributes = {};
	swfobject.embedSWF("paintprogram/paint.swf", "drawingCanvas", "400", "300", "9.0.0","", flashvars, params, attributes);
}