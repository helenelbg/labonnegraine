dhtmlXToolbarObject.prototype.openSelectMenu = function (id) {
    this.callMyEvent(this.objPull[this.idPrefix + id].arw, 'mouseover')
    this.callMyEvent(this.objPull[this.idPrefix + id].arw, 'mousedown')
};

dhtmlXToolbarObject.prototype.closeSelectMenu = function (id) {
    this.callMyEvent(this.objPull[this.idPrefix + id].arw, 'mouseout')
};

dhtmlXToolbarObject.prototype.callMyEvent = function (obj, name) {
    if (document.createEvent) {
        const e = document.createEvent('MouseEvents')
        e.initEvent(name, true, false);
        obj.dispatchEvent(e);
    } else if (document.createEventObject) {
        const e = document.createEventObject();
        e.button = 1;
        obj.fireEvent('on'+name, e)
    }
};

/*
Fonctions utiles
 */
function generateSelect(list, size)
{
    var html = "";
    if(list!=undefined)
    {
        var html_size = "";
        if(size!=undefined && size!=null && size!="" && size!=0)
            html_size = "style='width:"+size+" !important;'";

        html = "<select class='custom_combo_select' "+html_size+">";
        if(Array.isArray(list))
        {
            $(list).each(function( num, item ) {
                if(item!=undefined && item!=null && item!="")
                    html = html+"<option value='"+item+"'>"+item;
            });
        }
        else if(typeof list === 'object')
        {
            var entries = Object.entries(list);
            $(entries).each(function( num, item ) {
                if(item[0]!=undefined && item[0]!=null && item[0]!="")
                {
                    if(item[1]!=undefined && item[1]!=null && item[1]!="")
                    {
                        html = html+"<option value='"+item[0]+"'>"+item[1];
                    }
                    else
                    {
                        html = html+"<option value='"+item[0]+"'>"+item[0];
                    }
                }
            });
        }
        html = html+"</select>";
    }
    return html;
}
function isValidUrl(string) {
    let url;

    try {
        url = new URL(string);
    } catch (_) {
        return false;
    }

    return url.protocol === "http:" || url.protocol === "https:" || url.protocol === "mailto:" || url.protocol === "sms:" || url.protocol === "tel:";
}
function validateTime(timeValue)
{
    if(timeValue == "" || timeValue.indexOf(":")<0)
    {
        //alert("Invalid Time format");
        return false;
    }
    else
    {
        var sHours = timeValue.split(':')[0];
        var sMinutes = timeValue.split(':')[1];
        var sSecondes = timeValue.split(':')[2];

        if(sHours == "" || isNaN(sHours) || parseInt(sHours)>23)
        {
            //alert("Invalid Time format");
            return false;
        }
        else if(parseInt(sHours) == 0)
            sHours = "00";
        else if (sHours <10)
            sHours = "0"+sHours;

        if(sMinutes == "" || isNaN(sMinutes) || parseInt(sMinutes)>59)
        {
            //alert("Invalid Time format");
            return false;
        }
        else if(parseInt(sMinutes) == 0)
            sMinutes = "00";
        else if (sMinutes <10)
            sMinutes = "0"+sMinutes;

        if(sSecondes == "" || isNaN(sSecondes) || parseInt(sSecondes)>59)
        {
            alert("Invalid Time format");
            return false;
        }
        else if(parseInt(sSecondes) == 0)
            sSecondes = "00";
        else if (sSecondes <10)
            sSecondes = "0"+sSecondes;

        value = sHours + ":" + sMinutes + ":" + sSecondes;
    }

    return true;
}

/*
Input class
avec comportement
 */

$(document).on('input', '.input_float', function(e){
    this.value = this.value.replace(/[^0-9.-]/g, '').replace(/(\..*?)\..*/g, '$1');
});
$(document).on('input', '.input_floatpos', function(e){
    this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');
});
$(document).on('input', '.input_int', function() {
    this.value = this.value.replace(/[^0-9-]/g, '').replace(/(\..*?)\..*/g, '$1');
});
$(document).on('input', '.input_intpos', function() {
    this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*?)\..*/g, '$1');
});

var oldvalue_time = "";
$(document).on('focusin', '.input_time', function(e){
    var val = $(this).val();
    var isValid = validateTime(val);
    if(isValid)
        oldvalue_time = val;
    if(!isValid || val=="00:00:00")
        $(this).val("");
});
$(document).on('focusout', '.input_time', function(e){
    var val = $(this).val();
    var isValid = validateTime(val);
    if(!isValid || (val==null || val==''))
    {
        if(oldvalue_time!=undefined && oldvalue_time!=null && oldvalue_time!="")
            $(this).val(oldvalue_time);
        else
            $(this).val("00:00:00");
    }
    oldvalue_time = null;
});

/*
DHTMLX Config
 */
dhtmlXGridCellObject.prototype.getRaw = function () {
    if(this.cell.raw==undefined || this.cell.raw==null || this.cell.raw=="")
        this.cell.raw = "";
    return this.cell.raw;
};

/*
function eXcell_mytime(cell){                  // excell name is defined here
    if (cell){                                 // default pattern, just copy it
        this.cell = cell;
        this.grid = this.cell.parentNode.grid;
    }
    this.setValue=function(val){
        this.setCValue(val);
    }
    this.getValue=function(){
        return this.cell.innerHTML; // get value
    }
    this.edit=function(){
        this.val = this.getValue(); // save current value
        this.cell.innerHTML = "<input type='text' style='width:50px;'>"
            + "<select style='width:50px;'><option value='AM'>AM"
            + "<option value='PM'>PM</select>"; // editor's html
        this.cell.firstChild.value=parseInt(this.val); // set the first part of data
        if (this.val.indexOf("PM")!=-1) this.cell.childNodes[1].value="PM";
        // blocks onclick event
        this.cell.childNodes[0].onclick=function(e){ (e||event).cancelBubble=true;}
        // blocks onclick event
        this.cell.childNodes[1].onclick=function(e){ (e||event).cancelBubble=true;}
    }
    this.detach=function(){
        if(this.cell.childNodes[0]!=undefined && this.cell.childNodes[1]!=undefined)
        {
            // sets the new value
            this.setValue(this.cell.childNodes[0].value+" "+this.cell.childNodes[1].value);
        }
        return this.val!=this.getValue(); // compares the new and the old values
    }
}
eXcell_mytime.prototype = new eXcell;*/

/*
Champs custom
 */
/*function eXcell_edint(cell){                  // excell name is defined here
    if (cell){                                 // default pattern, just copy it
        this.cell = cell;
        this.grid = this.cell.parentNode.grid;
        if(this.cell.editing==undefined || this.cell.editing==null || this.cell.editing=="")
            this.cell.editing = 0;
    }
    this.setValue=function(val){
        if(this.cell.editing==0)
        {
            if(val!=undefined && val!=null)
            {
                val = parseInt(val*1);
                if (!Number.isNaN(val))
                {
                    this.setCValue(val);
                    return true;
                }
            }
        }
        val = this.val;
        if(val!=undefined && val!=null)
        {
            val = parseInt(val*1);
            if (!Number.isNaN(val))
            {
                this.setCValue(val);
                return true;
            }
        }
        this.setCValue("");
    }
    this.getValue=function(){
        if(this.cell.editing==0)
            return this.cell.innerHTML; // get value
        else
            return this.val; // get value
    }
    this.edit=function(){
        this.val = this.getValue(); // save current valueorysynch
        this.cell.editing = 1;
        this.cell.innerHTML = "<input type='text' class='input_int' style='width:90%;'>"
        ; // editor's html
        this.cell.firstChild.value=parseInt(this.val); // set the first part of data
        // blocks onclick event
        this.cell.childNodes[0].onclick=function(e){ (e||event).cancelBubble=true;}
    }
    this.detach=function(){
        this.cell.editing = 0;
        if(this.cell.childNodes[0]!=undefined)
        {
            // sets the new value
            this.setValue(Number(this.cell.childNodes[0].value));
        }
        return this.val!=this.getValue(); // compares the new and the old values
    }
}
eXcell_edint.prototype = new eXcell;*/
function eXcell_edint(cell){                  // excell name is defined here
    this.base = eXcell_ed;
    this.base(cell);
    if (cell){                                 // default pattern, just copy it
        this.cell = cell;
        this.grid = this.cell.parentNode.grid;
        if(this.cell.old==undefined || this.cell.old==null || this.cell.old=="")
            this.cell.old = 0;
    }
    this.setValue=function(val){
        if(val!=undefined && val!=null && val!="")
        {
            val = parseInt(val*1);
            if (!Number.isNaN(val))
            {
                this.setCValue(val);
                this.cell.old = val;
                return true;
            }
        }
        else
        {
            val =  this.cell.old;
            if(val!=undefined && val!=null && val!="")
            {
                val = parseInt(val*1);
                if (!Number.isNaN(val))
                {
                    this.setCValue(val);
                    this.cell.old = val;
                    return true;
                }
            }
        }
        this.setCValue("");
    }
    this.edit=function(){
        this.cell.atag = "INPUT";
        this.val = this.getValue();
        this.obj = document.createElement(this.cell.atag);
        this.obj.setAttribute("autocomplete", "off");
        this.obj.style.height = (this.cell.offsetHeight - 4) + "px";
        this.obj.className = "dhx_combo_edit input_int";
        this.obj.wrap = "soft";
        this.obj.style.textAlign = this.cell.style.textAlign;
        this.obj.onclick = function (c) {
            (c || event).cancelBubble = true
        };
        this.obj.onmousedown = function (c) {
            (c || event).cancelBubble = true
        };

        var value = this.val;
        if (value!=null && value!="" && !Number.isNaN(value))
        {
            value = parseInt(value);
        }
        else
        {
            value = 0;
        }
        this.obj.value = value;

        this.cell.innerHTML = "";
        this.cell.appendChild(this.obj);
        this.obj.onselectstart = function (c) {
            if (!c) {
                c = event
            }
            c.cancelBubble = true;
            return true
        };
        if (_isIE) {
            this.obj.focus();
            this.obj.blur()
        }
        this.obj.focus()
    }
}
eXcell_edint.prototype = new eXcell_ed;

/*function eXcell_edfloat(cell){                  // excell name is defined here
    if (cell){                                 // default pattern, just copy it
        this.cell = cell;
        this.grid = this.cell.parentNode.grid;
        if(this.cell.editing==undefined || this.cell.editing==null || this.cell.editing=="")
            this.cell.editing = 0;
    }
    this.setValue=function(val){
        if(this.cell.editing==0)
        {
            if(val!=undefined && val!=null)
            {
                val = parseFloat(val*1);
                if (!Number.isNaN(val))
                {
                    this.setCValue(val);
                    return true;
                }
            }
        }
        val = this.val;
        if(val!=undefined && val!=null)
        {
            val = parseFloat(val*1);
            if (!Number.isNaN(val))
            {
                this.setCValue(val);
                return true;
            }
        }
        this.setCValue("");
    }
    this.getValue=function(){
        if(this.cell.editing==0)
            return this.cell.innerHTML; // get value
        else
            return this.val; // get value
    }
    this.edit=function(){
        this.val = this.getValue(); // save current value
        this.cell.editing = 1;
        this.cell.innerHTML = "<input type='text' class='input_float' style='width:90%;'>"
        ; // editor's html
        this.cell.firstChild.value=parseFloat(this.val); // set the first part of data
        // blocks onclick event
        this.cell.childNodes[0].onclick=function(e){ (e||event).cancelBubble=true;}
    }
    this.detach=function(){
        this.cell.editing = 0;
        if(this.cell.childNodes[0]!=undefined)
        {
            // sets the new value
            this.setValue(Number(this.cell.childNodes[0].value));
        }
        return this.val!=this.getValue(); // compares the new and the old values
    }
}
eXcell_edfloat.prototype = new eXcell;*/
function eXcell_edfloat(cell){                  // excell name is defined here
    this.base = eXcell_ed;
    this.base(cell);
    if (cell){                                 // default pattern, just copy it
        this.cell = cell;
        this.grid = this.cell.parentNode.grid;
        if(this.cell.old==undefined || this.cell.old==null || this.cell.old=="")
            this.cell.old = 0;
    }
    this.setValue=function(val){
        if(val!=undefined && val!=null && val!="")
        {
            val = parseFloat(val*1);
            if (!Number.isNaN(val))
            {
                this.setCValue(val);
                this.cell.old = val;
                return true;
            }
        }
        else
        {
            val =  this.cell.old;
            if(val!=undefined && val!=null && val!="")
            {
                val = parseFloat(val*1);
                if (!Number.isNaN(val))
                {
                    this.setCValue(val);
                    this.cell.old = val;
                    return true;
                }
            }
        }
        this.setCValue("");
    }
    this.edit=function(){
        this.cell.atag = "INPUT";
        this.val = this.getValue();
        this.obj = document.createElement(this.cell.atag);
        this.obj.setAttribute("autocomplete", "off");
        this.obj.style.height = (this.cell.offsetHeight - 4) + "px";
        this.obj.className = "dhx_combo_edit input_float";
        this.obj.wrap = "soft";
        this.obj.style.textAlign = this.cell.style.textAlign;
        this.obj.onclick = function (c) {
            (c || event).cancelBubble = true
        };
        this.obj.onmousedown = function (c) {
            (c || event).cancelBubble = true
        };
        if(this.val!=null && this.val!="")
            this.obj.value = parseFloat(this.val);
        this.cell.innerHTML = "";
        this.cell.appendChild(this.obj);
        this.obj.onselectstart = function (c) {
            if (!c) {
                c = event
            }
            c.cancelBubble = true;
            return true
        };
        if (_isIE) {
            this.obj.focus();
            this.obj.blur()
        }
        this.obj.focus()
    }
}
eXcell_edfloat.prototype = new eXcell_ed;

function eXcell_edprice(cell){                  // excell name is defined here
    this.base = eXcell_edfloat;
    this.base(cell);
    if (cell){                                 // default pattern, just copy it
        this.cell = cell;
        this.grid = this.cell.parentNode.grid;
        if(this.cell.old==undefined || this.cell.old==null || this.cell.old=="")
            this.cell.old = 0;
    }
    this.setValue=function(val){
        if(val!=undefined && val!=null && val!="")
        {
            val = parseFloat(val*1);
            if (!Number.isNaN(val))
            {
                var formatted = priceFormat(parseFloat(val));
                this.setCValue(formatted);
                this.cell.old = val;
                return true;
            }
        }
        else
        {
            val =  this.cell.old;
            if(val!=undefined && val!=null && val!="")
            {
                val = parseFloat(val*1);
                if (!Number.isNaN(val))
                {
                    var formatted = priceFormat(parseFloat(val));
                    this.setCValue(formatted);
                    this.cell.old = val;
                    return true;
                }
            }
        }
        this.setCValue("");
    }
    this.edit=function(){
        this.cell.atag = "INPUT";
        this.val = this.getValue();
        this.obj = document.createElement(this.cell.atag);
        this.obj.setAttribute("autocomplete", "off");
        this.obj.style.height = (this.cell.offsetHeight - 4) + "px";
        this.obj.className = "dhx_combo_edit input_float";
        this.obj.wrap = "soft";
        this.obj.style.textAlign = this.cell.style.textAlign;
        this.obj.onclick = function (c) {
            (c || event).cancelBubble = true
        };
        this.obj.onmousedown = function (c) {
            (c || event).cancelBubble = true
        };

        var formatted = priceFormat(parseFloat(this.val));
        this.obj.value = formatted;
        this.cell.innerHTML = "";
        this.cell.appendChild(this.obj);
        this.obj.onselectstart = function (c) {
            if (!c) {
                c = event
            }
            c.cancelBubble = true;
            return true
        };
        if (_isIE) {
            this.obj.focus();
            this.obj.blur()
        }
        this.obj.focus()
    }
}
eXcell_edprice.prototype = new eXcell_edfloat;

function eXcell_edurl(cell){                  // excell name is defined here
    if (cell){                                 // default pattern, just copy it
        this.cell = cell;
        this.grid = this.cell.parentNode.grid;
        //console.log("in "+this.cell.editing);
        if(this.cell.editing==undefined || this.cell.editing==null || this.cell.editing=="")
            this.cell.editing = 0;
    }
    this.setValue=function(val){
        //console.log("setValue "+this.cell.editing);
        if(this.cell.editing==0)
        {
            if(val!=undefined && val!=null)
            {
                if (isValidUrl(val))
                    this.setCValue(val);
                else
                    this.setCValue(this.val);
            }
        }
    }
    this.getValue=function(){
        //console.log("getValue "+this.cell.editing);
        if(this.cell.editing==0)
            return this.cell.innerHTML; // get value
        else
            return this.val; // get value
    }
    this.edit=function(){
        //console.log("edit "+this.cell.editing);
        this.val = this.getValue(); // save current value
        this.cell.editing = 1;
        this.cell.innerHTML = "<input type='text' style='width:90%;'>"
        ; // editor's html
        this.cell.firstChild.value=this.val; // set the first part of data
        // blocks onclick event
        this.cell.childNodes[0].onclick=function(e){ (e||event).cancelBubble=true;}
    }
    this.detach=function(){
        //console.log("detach "+this.cell.editing);
        this.cell.editing = 0;
        if(this.cell.childNodes[0]!=undefined)
        {
            this.setValue(this.cell.childNodes[0].value);
        }
        return this.val!=this.getValue(); // compares the new and the old values
    }
}
eXcell_edurl.prototype = new eXcell;


/*function eXcell_edboolean(cell){                  // excell name is defined here
    if (cell){                                 // default pattern, just copy it
        this.cell = cell;
        this.grid = this.cell.parentNode.grid;
    }
    this.setValue=function(val){
        if (typeof val == "boolean" || (val==1 || val==0) || (val=="true" || val=="false") ) {
            if(val==1)
                val=true;
            else if(val==0)
                val=false;
            else if(val==="true")
                val=true;
            else if(val=="false")
                val=false;

            // Si json => sets the value in raw
            this.cell._attrs.raw = val;
            // sets the new value in cell
            var units = {false:lang_no, true:lang_yes};
            this.setCValue(units[val]);
        }
    }
    this.getValue=function(){
        return this.cell._attrs.raw;
        //return this.cell.innerHTML; // get value
    }
    this.edit=function(){
        this.val = Boolean(this.cell._attrs.raw); // save current value

        var units = {false:lang_no, true:lang_yes};

        this.cell.innerHTML = generateSelect(units, "90%"); // editor's html

        this.cell.firstChild.value= this.val; // set the first part of data
        // blocks onclick event
        this.cell.childNodes[0].onclick=function(e){ (e||event).cancelBubble=true;}
    }
    this.detach=function(){
        if(this.cell.childNodes[0]!=undefined)
        {
            if(this.cell.childNodes[0].value=="true")
                var value = true;
            else
                var value = false;
            this.setValue(value);
        }
        return this.val!=this.getValue(); // compares the new and the old values
    }
}
eXcell_edboolean.prototype = new eXcell;*/

function eXcell_coboolean(a) {
    this.base = eXcell_co;
    this.base(a);
    this.editable = false;
    if (a) {
        this.combo = new dhtmlXGridComboObject();
        this.combo.put(1, lang_yes);
        this.combo.put(0, lang_no);
        this.cell._combo = this.combo;

        var tmp_combo = new dhtmlXGridComboObject();
        tmp_combo.put(1);
        tmp_combo.put(0);
        this.grid.combos[this.cell._cellIndex] = tmp_combo;
        this.grid.setSelectFilterLabel(this.cell._cellIndex, setLabelBoolean);
    }
}
eXcell_coboolean.prototype = new eXcell_co;

function eXcell_coroboolean(a) {
    this.base = eXcell_co;
    this.base(a);
    this.editable = false;
    if (a) {
        this.combo = new dhtmlXGridComboObject();
        this.combo.put(1, lang_yes);
        this.combo.put(0, lang_no);
        this.cell._combo = this.combo;

        var tmp_combo = new dhtmlXGridComboObject();
        tmp_combo.put(1);
        tmp_combo.put(0);
        this.grid.combos[this.cell._cellIndex] = tmp_combo;
        this.grid.setSelectFilterLabel(this.cell._cellIndex, setLabelBoolean);

        //this.combo = (this.cell._combo || this.grid.getCombo(this.cell._cellIndex));
    }
    this.edit = function () {
    };
    this.isDisabled = function () {
        return true
    };
}
eXcell_coroboolean.prototype = new eXcell_co;

function setLabelBoolean(a)
{
    var ret = a;
    if(a!=undefined)
    {
        if(a=="1")
            ret = lang_yes;
        else if(a=="0")
            ret = lang_no;
    }
    return ret;
}
function setLabelCheckBox(a)
{
    var ret = a;
    if(a!=undefined)
    {
        if(a=="1")
            ret = lang_check;
        else if(a=="0")
            ret = lang_uncheck;
    }
    return ret;
}


dhtmlXGridObject.prototype._in_header_checkbox_filter = function (c, a) {
    c.innerHTML = "<select></select>";
    c.onclick = function (g) {
        (g || event).cancelBubble = true;
        return false
    };
    this.makeCustomFilter(c.firstChild, a, "checkbox");
};
dhtmlXGridObject.prototype.makeCustomFilter = function (h, e, type) {
    if (!this.filters) {
        this.filters = []
    }
    if (typeof (h) != "object") {
        h = document.getElementById(h)
    }
    if (!h) {
        return
    }
    var a = this;
    if (!h.style.width) {
        h.style.width = "90%"
    }
    if (h.tagName == "SELECT" && type=="checkbox") {
        this.setSelectFilterLabel(e, setLabelCheckBox);

        this.filters.push([h, e]);
        this._loadSelectOptins(h, e);
        h.onchange = function () {
            a.filterByAll()
        };
        if (_isIE) {
            h.style.marginTop = "1px"
        }
        this.attachEvent("onEditCell", function (m, l, n) {
            this._build_m_order();
            if (m == 2 && this.filters && (this._m_order ? (n == this._m_order[e]) : (n == e))) {
                this._loadSelectOptins(h, e)
            }
            return true
        })
    }
    if (h.parentNode) {
        h.parentNode.className += " filter"
    }
    this._filters_ready()
};

/*
function eXcell_edcalendar(a) {
    if (a) {
        this.cell = a;
        this.grid = this.cell.parentNode.grid;
        if (!this.grid._grid_calendarSc) {
            var e = (this.grid._grid_calendarSc = new dhtmlxCalendarObject());
            this.grid.callEvent("onDhxCalendarCreated", [e]);
            var c = this.grid;
            e.attachEvent("onClick", function () {
                this._last_operation_calendar = true;
                window.setTimeout(function () {
                    c.editStop();
                }, 1);
                return true;
            });
            var g = function (h) {
                (h || event).cancelBubble = true;
            };
            dhtmlxEvent(e.base, "click", g);
        }
    }
}
eXcell_edcalendar.prototype = new eXcell();
eXcell_edcalendar.prototype.edit = function () {
    var c = this.grid.getPosition(this.cell);
    this.grid._grid_calendarSc._show(false, false);
    this.grid._grid_calendarSc.setPosition(c[0] * 1, c[1] * 1 +this.cell.offsetHeight-4);
    this.grid.callEvent("onCalendarShow", [this.grid._grid_calendarSc, this.cell.parentNode.idd, this.cell._cellIndex]);
    this.grid._grid_calendarSc._last_operation_calendar = false;
    this.cell._cediton = true;
    this.val = this.cell.val;
    this._val = this.cell.innerHTML;
    var a = this.grid._grid_calendarSc.draw;
    this.grid._grid_calendarSc.draw = function () {};
    this.grid._grid_calendarSc.setDateFormat(this.grid._dtmask || "%Y-%m-%d");
    this.grid._grid_calendarSc.setDate(this.val);
    this.grid._grid_calendarSc.hideTime();
    this.grid._grid_calendarSc.draw = a;
    this.cell.atag = !this.grid.multiLine && (_isKHTML || _isMacOS || _isFF) ? "INPUT" : "TEXTAREA";
    this.obj = document.createElement(this.cell.atag);
    this.obj.style.height = this.cell.offsetHeight - 4 + "px";
    this.obj.className = "dhx_combo_edit";
    this.obj.wrap = "soft";
    this.obj.style.textAlign = this.cell.align;
    this.obj.onclick = function (g) {
        (g || event).cancelBubble = true;
    };
    this.obj.onmousedown = function (g) {
        (g || event).cancelBubble = true;
    };
    this.obj.value = this.getValue();
    this.cell.innerHTML = "";
    this.cell.appendChild(this.obj);
    if (window.dhx4.isIE) {
        this.obj.style.overflow = "visible";
        if (this.grid.multiLine && this.obj.offsetHeight >= 18 && this.obj.offsetHeight < 40) {
            this.obj.style.height = "36px";
            this.obj.style.overflow = "scroll";
        }
    }
    this.obj.onselectstart = function (g) {
        if (!g) {
            g = event;
        }
        g.cancelBubble = true;
        return true;
    };
    this.obj.focus();
    this.obj.focus();
};
eXcell_edcalendar.prototype.getDate = function () {
    if (this.cell.val) {
        return this.cell.val;
    }
    return null;
};
eXcell_edcalendar.prototype.getValue = function () {
    if (this.cell._clearCell) {
        return "";
    }
    if (this.grid._dtmask_inc && this.cell.val) {
        return this.grid._grid_calendarSc.getFormatedDate(this.grid._dtmask_inc, this.cell.val).toString();
    }
    return this.cell.innerHTML.toString()._dhx_trim();
};
eXcell_edcalendar.prototype.detach = function () {
    if (!this.grid._grid_calendarSc) {
        return;
    }
    this.grid._grid_calendarSc.hide();
    if (this.cell._cediton) {
        this.cell._cediton = false;
    } else {
        return;
    }
    if (this.grid._grid_calendarSc._last_operation_calendar) {
        this.grid._grid_calendarSc._last_operation_calendar = false;
        var e = this.grid._grid_calendarSc.getFormatedDate(this.grid._dtmask || "%Y-%m-%d");
        var c = this.grid._grid_calendarSc.getDate();
        this.cell.val = new Date(c);
        this.setCValue(e, c);
        this.cell._clearCell = !e;
        var a = this.val;
        this.val = this._val;
        return this.cell.val.valueOf() != (a || "").valueOf();
    }
    this.setValue(this.obj.value);
    var a = this.val;
    this.val = this._val;
    return this.cell.val.valueOf() != (a || "").valueOf();
};
eXcell_edcalendar.prototype.setValue = function (a) {
    if (a && typeof a == "object") {
        this.cell.val = a;
        this.cell._clearCell = false;
        this.setCValue(this.grid._grid_calendarSc.getFormatedDate(this.grid._dtmask || "%Y-%m-%d", a).toString(), this.cell.val);
        return;
    }
    if (!a || a.toString()._dhx_trim() == "") {
        a = "&nbsp";
        this.cell._clearCell = true;
        this.cell.val = "";
    } else {
        this.cell._clearCell = false;
        this.cell.val = new Date(this.grid._grid_calendarSc.setFormatedDate(this.grid._dtmask_inc || this.grid._dtmask || "%Y-%m-%d", a.toString(), null, true));
        if (this.grid._dtmask_inc) {
            a = this.grid._grid_calendarSc.getFormatedDate(this.grid._dtmask || "%Y-%m-%d", this.cell.val);
        }
    }
    if (this.cell.val == "NaN" || this.cell.val == "Invalid Date") {
        this.cell.val = new Date();
        this.cell._clearCell = true;
        this.setCValue("&nbsp;", 0);
    } else {
        this.setCValue((a || "").toString(), this.cell.val);
    }
};*/

function eXcell_edcalendar(cell){                  // excell name is defined here
    if (cell){                                 // default pattern, just copy it
        this.cell = cell;
        this.grid = this.cell.parentNode.grid;
        if(this.cell.editing==undefined || this.cell.editing==null || this.cell.editing=="")
            this.cell.editing = 0;
    }
    this.setValue=function(value){
        /*console.log("setValue");
        console.log(value);*/
        if(value.length==10)
        {
            if(value!=this.cell._attrs.raw || this.cell.editing)
            {
                //console.log("=>"+value);
                this.cell.editing = 0;
                // Si json => sets the value in raw
                this.cell._attrs.raw = value;
                // sets the new value in cell
                this.setCValue(value);
                return true;
            }
        }

        if(this.cell._attrs.raw!=undefined)
        {
            value = this.cell._attrs.raw;
            if(value.length==10)
            {
                //console.log("=>"+value);
                this.setCValue(this.cell._attrs.raw);
                return true;
            }
        }
        this.setCValue("");
    }
    this.getValue=function(){
        // console.log("getValue");
        return this.cell._attrs.raw;
        //return this.cell.innerHTML; // get value
    }
    this.edit=function(){
        //console.log("edit");
        this.cell.editing = 1;
        this.val = this.cell._attrs.raw; // save current value
        var value = this.cell._attrs.raw;

        this.cell.innerHTML = "<input type='text' class='input_date' style='width:90%;'>";

        this.cell.firstChild.value=value; // set the first part of data
        //this.cell.childNodes[1].value=hour;

        var elem = this.cell.querySelector(".input_date");
        var rangepicker = new Datepicker(elem, {language: "fr", format: "yyyy-mm-dd"});

        // blocks onclick event
        this.cell.querySelector(".input_date").onclick=function(e){ (e||event).cancelBubble=true;}
        // blocks onclick event
        this.cell.querySelector(".datepicker").onclick=function(e){ (e||event).cancelBubble=true;}
    }
    this.detach=function(){
        //console.log("detach");
        if(this.cell.querySelector(".input_date")!=undefined)
        {
            var value = this.cell.querySelector(".input_date").value;
            this.setValue(value);
        }
        else
            this.cell.editing = 0;
        return this.val!=this.getValue(); // compares the new and the old values
    }
}
eXcell_edcalendar.prototype = new eXcell;


/*function eXcell_edcalendartime(a) {
    if (a) {
        this.cell = a;
        this.grid = this.cell.parentNode.grid;
        if (!this.grid._grid_calendarSc) {
            var e = (this.grid._grid_calendarSc = new dhtmlxCalendarObject());
            this.grid.callEvent("onDhxCalendarCreated", [e]);
            var c = this.grid;
            e.attachEvent("onClick", function () {
                this._last_operation_calendar = true;
                window.setTimeout(function () {
                    c.editStop();
                }, 1);
                return true;
            });
            var g = function (h) {
                (h || event).cancelBubble = true;
            };
            dhtmlxEvent(e.base, "click", g);
        }
    }
}
eXcell_edcalendartime.prototype = new eXcell();
eXcell_edcalendartime.prototype.edit = function () {
    var value = this.cell._attrs.raw;
    var value_formated = "";
    if(value!=undefined && value!=null && value!="" && value!=0)
    {
        var temps = value.split("T");
        value_formated = temps[0];
        if(temps[1]!=undefined && temps[1]!=null && temps[1]!="" && temps[1]!=0)
        {
            var temps = temps[1].split("+");
            value_formated = value_formated+" "+temps[0].substring(0,5);
        }
        else
            value_formated = value_formated+" 00:00";
    }

    var c = this.grid.getPosition(this.cell);
    this.grid._grid_calendarSc._show(false, false);
    this.grid._grid_calendarSc.setPosition(c[0] * 1, c[1] * 1 +this.cell.offsetHeight-4);
    this.grid.callEvent("onCalendarShow", [this.grid._grid_calendarSc, this.cell.parentNode.idd, this.cell._cellIndex]);
    this.grid._grid_calendarSc._last_operation_calendar = false;
    this.cell._cediton = true;
    this.val = this.cell.val;
    this._val = this.cell.innerHTML;
    var a = this.grid._grid_calendarSc.draw;
    this.grid._grid_calendarSc.draw = function () {};
    this.grid._grid_calendarSc.setDateFormat(this.grid._dtmask || "%Y-%m-%d %H:%i");
    this.grid._grid_calendarSc.setDate(value_formated);
    //this.grid._grid_calendarSc.hideTime();
    this.grid._grid_calendarSc.draw = a;
    this.cell.atag = !this.grid.multiLine && (_isKHTML || _isMacOS || _isFF) ? "INPUT" : "TEXTAREA";
    this.obj = document.createElement(this.cell.atag);
    this.obj.style.height = this.cell.offsetHeight - 4 + "px";
    this.obj.className = "dhx_combo_edit";
    this.obj.wrap = "soft";
    this.obj.style.textAlign = this.cell.align;
    this.obj.onclick = function (g) {
        (g || event).cancelBubble = true;
    };
    this.obj.onmousedown = function (g) {
        (g || event).cancelBubble = true;
    };
    this.obj.value = value_formated;

    this.cell.innerHTML = "";
    this.cell.appendChild(this.obj);
    if (window.dhx4.isIE) {
        this.obj.style.overflow = "visible";
        if (this.grid.multiLine && this.obj.offsetHeight >= 18 && this.obj.offsetHeight < 40) {
            this.obj.style.height = "36px";
            this.obj.style.overflow = "scroll";
        }
    }
    this.obj.onselectstart = function (g) {
        if (!g) {
            g = event;
        }
        g.cancelBubble = true;
        return true;
    };
    this.obj.focus();
    this.obj.focus();
};
eXcell_edcalendartime.prototype.getDate = function () {
    if (this.cell.val) {
        return this.cell.val;
    }
    return null;
};
eXcell_edcalendartime.prototype.getValue = function () {
    if (this.cell._clearCell) {
        return "";
    }
    if (this.grid._dtmask_inc && this.cell.val) {
        return this.grid._grid_calendarSc.getFormatedDate(this.grid._dtmask_inc, this.cell.val).toString();
    }
    return this.cell._attrs.raw;
};
eXcell_edcalendartime.prototype.detach = function () {
    if (!this.grid._grid_calendarSc) {
        return;
    }
    this.grid._grid_calendarSc.hide();
    if (this.cell._cediton) {
        this.cell._cediton = false;
    } else {
        return;
    }
    if (this.grid._grid_calendarSc._last_operation_calendar) {
        this.grid._grid_calendarSc._last_operation_calendar = false;
        var e = this.grid._grid_calendarSc.getFormatedDate(this.grid._dtmask || "%Y-%m-%d %H:%i");
        var c = this.grid._grid_calendarSc.getDate();
        this.cell.val = new Date(c);
        this.setCValue(e, c);

        var value = e.replace(" ", "T")+":00+00:00";
        this.cell._attrs.raw = value;
        this.cell._clearCell = !e;
        var a = this.val;
        this.val = this._val;
        return this.cell.val.valueOf() != (a || "").valueOf();
    }
    var value = this.obj.value.replace(" ", "T")+":00+00:00";
    this.setValue(value);
    var a = this.val;
    this.val = this._val;
    return this.cell.val.valueOf() != (a || "").valueOf();
};
eXcell_edcalendartime.prototype.setValue = function (a) {
    if (a && typeof a == "object") {
        this.cell.val = a;
        this.cell._clearCell = false;
        this.setCValue(this.grid._grid_calendarSc.getFormatedDate(this.grid._dtmask || "%Y-%m-%d %H:%i", a).toString(), this.cell.val);
        return;
    }
    if (!a || a.toString()._dhx_trim() == "") {
        a = "&nbsp";
        this.cell._clearCell = true;
        this.cell.val = "";
    } else {
        this.cell._clearCell = false;
        this.cell.val = new Date(this.grid._grid_calendarSc.setFormatedDate(this.grid._dtmask_inc || this.grid._dtmask || "%Y-%m-%d %H:%i", a.toString(), null, true));
        if (this.grid._dtmask_inc) {
            a = this.grid._grid_calendarSc.getFormatedDate(this.grid._dtmask || "%Y-%m-%d %H:%i", this.cell.val);
        }
    }
    if (this.cell.val == "NaN" || this.cell.val == "Invalid Date") {
        this.cell.val = new Date();
        this.cell._clearCell = true;
        this.setCValue("&nbsp;", 0);
    } else {
        this.setCValue((a || "").toString(), this.cell.val);
    }
};*/

/*function eXcell_edcalendartime(cell){                  // excell name is defined here
    if (cell){                                 // default pattern, just copy it
        this.cell = cell;
        this.grid = this.cell.parentNode.grid;
        if(this.cell.editing==undefined || this.cell.editing==null || this.cell.editing=="")
            this.cell.editing = 0;
    }
    this.setValue=function(value){
        //console.log("datetime");
        //console.log("setValue");
        //console.log(value+" => "+value.length);
        if(value.length==19 && value.indexOf("T")==-1)
        {
            var date = "";
            var hour = "";
            if(value!=undefined && value!=null && value!="" && value!=0)
            {
                var temps = value.split(" ");
                date = temps[0];
                hour = temps[1];
            }

            if(value!=this.cell._attrs.raw || this.cell.editing)
            {
                //console.log(value);
                this.cell.editing = 0;
                // Si json => sets the value in raw
                this.cell._attrs.raw = value;
                // sets the new value in cell
                this.setCValue(date+" "+hour);
                return true;
            }
        }

        if(this.cell._attrs.raw!=undefined)
        {
            value = this.cell._attrs.raw;
            if(value.length==19 && value.indexOf("T")==-1)
            {
                var date = "";
                var hour = "";
                var temps = value.split(" ");
                date = temps[0];
                hour = temps[1];
                //console.log("=> "+value);
                this.setCValue(date+" "+hour);
                return true;
            }
        }
        this.setCValue("");
    }
    this.getValue=function(){
       // console.log("getValue");
        return this.cell._attrs.raw;
        //return this.cell.innerHTML; // get value
    }
    this.edit=function(){
        //console.log("edit");
        this.cell.editing = 1;
        this.val = this.cell._attrs.raw; // save current value
        var value = this.cell._attrs.raw;
        var date = "";
        var hour = "";
        if(value!=undefined && value!=null && value!="" && value!=0)
        {
            if((value.length==19 && value.indexOf("T")==-1))
            {
                var temps = value.split(" ");
                date = temps[0];
                hour = temps[1];
            }
        }

        var val = date+" "+hour;
        this.cell._attrs.raw = val;
        this.val = val;

        this.cell.innerHTML = "<input type='text' class='input_date' style='width:45%;'><input type='text' class='input_time' style='width:45%;'>";

        this.cell.firstChild.value=date; // set the first part of data
        //this.cell.childNodes[1].value=hour;

        var elem = this.cell.querySelector(".input_date");
        var rangepicker = new Datepicker(elem, {language: "fr", format: "yyyy-mm-dd"});

        $(".input_time").inputmask({regex: "^[0-2][0-9]:[0-5][0-9]:[0-5][0-9]$",oncomplete: function(){
                var val = $(this).val();
                var isValid = validateTime(val);
                if(!isValid) {
                    if(oldvalue_time!=undefined && oldvalue_time!=null && oldvalue_time!="")
                        $(this).val(oldvalue_time);
                    else
                        $(this).val("00:00:00");
                }
            }});  //static mask
        $(".input_time").val(hour);

        // blocks onclick event
        this.cell.querySelector(".input_date").onclick=function(e){ (e||event).cancelBubble=true;}
        // blocks onclick event
        this.cell.querySelector(".input_time").onclick=function(e){ (e||event).cancelBubble=true;}
        // blocks onclick event
        this.cell.querySelector(".datepicker").onclick=function(e){ (e||event).cancelBubble=true;}
    }
    this.detach=function(){
        //console.log("detach");
        if(this.cell.querySelector(".input_date")!=undefined && this.cell.querySelector(".input_time")!=undefined)
        {
            var value = this.cell.querySelector(".input_date").value+" "+this.cell.querySelector(".input_time").value;
            this.setValue(value);
        }
        else
            this.cell.editing = 0;
        return this.val!=this.getValue(); // compares the new and the old values
    }
}
eXcell_edcalendartime.prototype = new eXcell;*/

function eXcell_edcalendartime(cell){                  // excell name is defined here
    if (cell){                                 // default pattern, just copy it
        this.cell = cell;
        this.grid = this.cell.parentNode.grid;
        if(this.cell.editing==undefined || this.cell.editing==null || this.cell.editing=="")
            this.cell.editing = 0;
    }
    this.setValue=function(value){
        //console.log("datetime");
        /*console.log("setValue");
        console.log(value+" => "+value.length);*/
        if(value.length==19 && value.indexOf("T")==-1)
        {
            var date = "";
            var hour = "";
            if(value!=undefined && value!=null && value!="" && value!=0)
            {
                var temps = value.split(" ");
                date = temps[0];
                hour = temps[1];
            }

            if(value!=this.cell._attrs.raw || this.cell.editing)
            {
                // sets the new value in cell
                this.setCValue(date+" "+hour);
                return true;
            }
        }

        this.setCValue("");
    }
    this.edit=function(){
        this.cell.atag = "INPUT";
        this.val = this.getValue(); // save current value
        var value = this.val;
        var date = "";
        var hour = "";
        if(value!=undefined && value!=null && value!="" && value!=0)
        {
            if((value.length==19 && value.indexOf("T")==-1))
            {
                var temps = value.split(" ");
                date = temps[0];
                hour = temps[1];
            }
        }

        var val = date+" "+hour;
        this.val = val;

        this.obj_date = document.createElement(this.cell.atag);
        this.obj_date.setAttribute("autocomplete", "off");
        this.obj_date.style.height = (this.cell.offsetHeight - 4) + "px";
        this.obj_date.style.width = "48%";
        this.obj_date.style.float = "left";
        this.obj_date.className = "dhx_combo_edit input_date";
        this.obj_date.wrap = "soft";
        this.obj_date.style.textAlign = this.cell.style.textAlign;
        this.obj_date.onclick = function (c) {
            (c || event).cancelBubble = true
        };
        this.obj_date.onmousedown = function (c) {
            (c || event).cancelBubble = true
        };
        this.obj_date.value = date;
        this.cell.innerHTML = "";
        this.cell.appendChild(this.obj_date);
        this.obj_date.onselectstart = function (c) {
            if (!c) {
                c = event
            }
            c.cancelBubble = true;
            return true
        };
        var elem = this.cell.querySelector(".input_date");
        var rangepicker = new Datepicker(elem, {language: "fr", format: "yyyy-mm-dd"});
        var datepicker = this.cell.querySelector(".datepicker");
        datepicker.onclick = function (c) {
            (c || event).cancelBubble = true
        };
        datepicker.onmousedown = function (c) {
            (c || event).cancelBubble = true
        };
        datepicker.onselectstart = function (c) {
            if (!c) {
                c = event
            }
            c.cancelBubble = true;
            return true
        };

        this.obj_hour = document.createElement("INPUT");
        this.obj_hour.setAttribute("autocomplete", "off");
        this.obj_hour.style.height = (this.cell.offsetHeight - 4) + "px";
        this.obj_hour.style.width = "48%";
        this.obj_hour.style.float = "left";
        this.obj_hour.className = "dhx_combo_edit input_time";
        this.obj_hour.wrap = "soft";
        this.obj_hour.style.textAlign = this.cell.style.textAlign;
        this.obj_hour.onclick = function (c) {
            (c || event).cancelBubble = true
        };
        this.obj_hour.onmousedown = function (c) {
            (c || event).cancelBubble = true
        };
        this.obj_hour.value = hour;
        this.cell.appendChild(this.obj_hour);
        this.obj_hour.onselectstart = function (c) {
            if (!c) {
                c = event
            }
            c.cancelBubble = true;
            return true
        };
        $(".input_time").inputmask({regex: "^[0-2][0-9]:[0-5][0-9]:[0-5][0-9]$",oncomplete: function(){
                var val = $(this).val();
                var isValid = validateTime(val);
                if(!isValid) {
                    if(oldvalue_time!=undefined && oldvalue_time!=null && oldvalue_time!="")
                        $(this).val(oldvalue_time);
                    else
                        $(this).val("00:00:00");
                }
            }});  //static mask

        if (_isIE) {
            this.obj_date.focus();
            this.obj_date.blur()
        }
        this.obj_date.focus();
    }
    this.detach=function(){
        //console.log("detach");
        if(this.cell.querySelector(".input_date")!=undefined && this.cell.querySelector(".input_time")!=undefined)
        {
            var value = this.cell.querySelector(".input_date").value+" "+this.cell.querySelector(".input_time").value;
            this.setValue(value);
        }
        return this.val!=this.getValue(); // compares the new and the old values
    }
    this.getValue = function () {
        //console.log("getValue");
        if (this.cell.firstChild && this.cell.atag && this.cell.firstChild.tagName == this.cell.atag) {
            if(this.cell.querySelector(".input_date")!=undefined && this.cell.querySelector(".input_time")!=undefined)
            {
                var value = this.cell.querySelector(".input_date").value + " " + this.cell.querySelector(".input_time").value;
                //return this.cell.firstChild.value;
            }
        }
        if (this.cell._clearCell) {
            //console.log("''");
            return "";
        }
        return this.cell.innerHTML.toString()._dhx_trim();
    };
}
eXcell_edcalendartime.prototype = new eXcell;

function eXcell_edcalendartimeiso(cell){                  // excell name is defined here
    if (cell){                                 // default pattern, just copy it
        this.cell = cell;
        this.grid = this.cell.parentNode.grid;
        if(this.cell.editing==undefined || this.cell.editing==null || this.cell.editing=="")
            this.cell.editing = 0;
    }
    this.setValue=function(value){
        //console.log("datetime");
        //console.log("setValue");
        if(value.length==25 && value.indexOf("T")>=0)
        {
            var date = "";
            var hour = "";
            if(value!=undefined && value!=null && value!="" && value!=0)
            {
                var temps = value.split("T");
                date = temps[0];
                if(temps[1]!=undefined && temps[1]!=null && temps[1]!="" && temps[1]!=0)
                {
                    var temps = temps[1].split("+");
                    hour = temps[0];
                }
                else
                    hour = "00:00:00";
            }

            if(value!=this.cell._attrs.raw || this.cell.editing)
            {
                //console.log(value);
                this.cell.editing = 0;
                // Si json => sets the value in raw
                this.cell._attrs.raw = value;
                // sets the new value in cell
                this.setCValue(date+" "+hour.substring(0,8));
                return true;
            }
        }

        if(this.cell._attrs.raw!=undefined)
        {
            value = this.cell._attrs.raw;
            if(value.length==25 && value.indexOf("T")>=0)
            {
                var date = "";
                var hour = "";
                if(value!=undefined && value!=null && value!="" && value!=0)
                {
                    var temps = value.split("T");
                    date = temps[0];
                    if(temps[1]!=undefined && temps[1]!=null && temps[1]!="" && temps[1]!=0)
                    {
                        var temps = temps[1].split("+");
                        hour = temps[0];
                    }
                    else
                        hour = "00:00:00";
                }
                //console.log("=> "+value);
                this.setCValue(date+" "+hour.substring(0,8));
                return true;
            }
        }
        this.setCValue("");
    }
    this.getValue=function(){
       // console.log("getValue");
        return this.cell._attrs.raw;
        //return this.cell.innerHTML; // get value
    }
    this.edit=function(){
        //console.log("edit");
        this.cell.editing = 1;
        this.val = this.cell._attrs.raw; // save current value
        var value = this.cell._attrs.raw;
        var date = "";
        var hour = "";
        if(value!=undefined && value!=null && value!="" && value!=0)
        {
            if(value!=undefined && value!=null && value!="" && value!=0)
            {
                var temps = value.split("T");
                date = temps[0];
                if(temps[1]!=undefined && temps[1]!=null && temps[1]!="" && temps[1]!=0)
                {
                    var temps = temps[1].split("+");
                    hour = temps[0];
                }
                else
                    hour = "00:00:00";
            }
        }

        var val = date+"T"+hour+"+00:00";
        this.cell._attrs.raw = val;
        this.val = val;

        this.cell.innerHTML = "<input type='text' class='input_date' style='width:45%;'><input type='text' class='input_time' style='width:45%;'>";

        this.cell.firstChild.value=date; // set the first part of data
        //this.cell.childNodes[1].value=hour;

        var elem = this.cell.querySelector(".input_date");
        var rangepicker = new Datepicker(elem, {language: "fr", format: "yyyy-mm-dd"});

        $(".input_time").inputmask({regex: "^[0-2][0-9]:[0-5][0-9]:[0-5][0-9]$",oncomplete: function(){
                var val = $(this).val();
                var isValid = validateTime(val);
                if(!isValid) {
                    if(oldvalue_time!=undefined && oldvalue_time!=null && oldvalue_time!="")
                        $(this).val(oldvalue_time);
                    else
                        $(this).val("00:00:00");
                }
            }});  //static mask
        $(".input_time").val(hour);

        // blocks onclick event
        this.cell.querySelector(".input_date").onclick=function(e){ (e||event).cancelBubble=true;}
        // blocks onclick event
        this.cell.querySelector(".input_time").onclick=function(e){ (e||event).cancelBubble=true;}
        // blocks onclick event
        this.cell.querySelector(".datepicker").onclick=function(e){ (e||event).cancelBubble=true;}
    }
    this.detach=function(){
        //console.log("detach");
        if(this.cell.querySelector(".input_date")!=undefined && this.cell.querySelector(".input_time")!=undefined)
        {
            var value = this.cell.querySelector(".input_date").value+"T"+this.cell.querySelector(".input_time").value+"+00:00";
            this.setValue(value);
        }
        else
            this.cell.editing = 0;
        return this.val!=this.getValue(); // compares the new and the old values
    }
}
eXcell_edcalendartimeiso.prototype = new eXcell;

function eXcell_edcolor(cell){                  // excell name is defined here
    if (cell){                                 // default pattern, just copy it
        this.cell = cell;
        this.grid = this.cell.parentNode.grid;
        if(this.cell.editing==undefined || this.cell.editing==null || this.cell.editing=="")
            this.cell.editing = 0;
    }
    this.setValue=function(value){
        if(value.length==7)
        {
            if(value!=this.cell._attrs.raw || this.cell.editing)
            {
                this.cell.editing = 0;
                // Si json => sets the value in raw
                this.cell._attrs.raw = value;
                // sets the new value in cell
                this.setCValue("<div class='edcolor_pre' style='background-color: "+value+"'></div>"+value+"");
                return true;
            }
        }

        if(this.cell._attrs.raw!=undefined)
        {
            value = this.cell._attrs.raw;
            if(value.length==7)
            {
                this.setCValue("<div class='edcolor_pre' style='background-color: "+value+"'></div>"+value+"");
                return true;
            }
        }
        this.cell._attrs.raw = "";
        this.setCValue("<div class='edcolor_pre' style='background-color: ;'></div>");
        /*this.cell._attrs.raw = "#ffffff";
        this.setCValue("<div class='edcolor_pre' style='background-color: #ffffff'></div>#ffffff");*/
    }
    this.getValue=function(){
       // console.log("getValue");
        return this.cell._attrs.raw;
        //return this.cell.innerHTML; // get value
    }
    this.edit=function(){
        //console.log("edit");
        this.cell.editing = 1;
        this.val = this.cell._attrs.raw; // save current value
        var value = this.cell._attrs.raw;

        this.cell.innerHTML = "<input type='text' class='input_coloris' style='width:90%;'>";
        this.cell.firstChild.value=value; // set the first part of data

        $('.input_coloris').ColorPicker({
            eventName: "focus",
            onSubmit: function(hsb, hex, rgb, el) {
                $(el).val("#"+hex);
                $(el).ColorPickerHide();
            },
            onBeforeShow: function () {
                $(this).ColorPickerSetColor(this.value);
            }
        })
        .bind('keyup', function(){
            $(this).ColorPickerSetColor(this.value);
        }).ColorPickerShow();

        // blocks onclick event
        this.cell.querySelector(".input_coloris").onclick=function(e){ (e||event).cancelBubble=true;}
        document.querySelector(".colorpicker").onclick=function(e){ (e||event).cancelBubble=true;}
    }
    this.detach=function(){
        $(".colorpicker").remove();
        //console.log("detach");
        if(this.cell.querySelector(".input_coloris")!=undefined)
        {
            var value = this.cell.querySelector(".input_coloris").value;
            this.setValue(value);
        }
        else
            this.cell.editing = 0;
        return this.val!=this.getValue(); // compares the new and the old values
    }
}
eXcell_edcolor.prototype = new eXcell;

function eXcell_rocolor(cell) {
    if (cell){
        this.cell = cell;
        this.grid = this.cell.parentNode.grid;
        if(this.cell.editing==undefined || this.cell.editing==null || this.cell.editing=="")
            this.cell.editing = 0;
    }
    this.editable = false;
    this.setValue=function(value){
        if(value.length==7)
        {
            if(value!=this.cell._attrs.raw || this.cell.editing)
            {
                this.cell.editing = 0;
                // Si json => sets the value in raw
                this.cell._attrs.raw = value;
                // sets the new value in cell
                this.setCValue("<div class='edcolor_pre' style='background-color: "+value+"'></div>"+value+"");
                return true;
            }
        }

        if(this.cell._attrs.raw!=undefined)
        {
            value = this.cell._attrs.raw;
            if(value.length==7)
            {
                this.setCValue("<div class='edcolor_pre' style='background-color: "+value+"'></div>"+value+"");
                return true;
            }
        }
        this.cell._attrs.raw = "";
        this.setCValue("<div class='edcolor_pre' style='background-color: ;'></div>");
        /*this.cell._attrs.raw = "#ffffff";
        this.setCValue("<div class='edcolor_pre' style='background-color: #ffffff'></div>#ffffff");*/
    }
    this.getValue=function(){
        // console.log("getValue");
        return this.cell._attrs.raw;
        //return this.cell.innerHTML; // get value
    }
    this.edit=function(){
    }
    this.isDisabled = function () {
        return true
    };
}
eXcell_rocolor.prototype = new eXcell;

function eXcell_rodatetime(a) {
    if (a) {
        this.cell = a;
        this.grid = this.cell.parentNode.grid;
        if(this.cell.raw==undefined || this.cell.raw==null || this.cell.raw=="")
            this.cell.raw = "";
    }
    this.getValue = function () {
        return this.cell.raw;
        if (this.cell.firstChild && this.cell.atag && this.cell.firstChild.tagName == this.cell.atag) {
            return this.cell.firstChild.value;
        }

        if (this.cell._clearCell) {
            return "";
        }
        return _isIE ? this.cell.innerText : this.cell.textContent;
    };
    this.setValue = function (c) {
        if (!c || c.toString()._dhx_trim() == "") {
            c = " ";
            this.cell._clearCell = true;
        } else {
            this.cell._clearCell = false;
        }

        this.cell.raw = c;

        var formated = datetimeFormat_usToFrReadable(c);
        this.setCValue(formated);
    };
}
eXcell_rodatetime.prototype = new eXcell_ed();
function setFilterAndSort_for_rodatetime (grid, column_index)
{
    grid.setCustomSorting(sort_datetime_readable,column_index);
    grid.getFilterElement(column_index)._filter = function(){
        var input = this.value; // gets the text of the filter input
        return function(value, id){
            var val=grid.cells(id,column_index).getTitle() // gets the value of the current cell
            // checks if the value of a cell has the text from the filter
            if (val.toLowerCase().indexOf(input.toLowerCase())!==-1){
                return true;
            }
            return false;
        }
    }
}
function sort_datetime_readable(a,b,order){
    return (a.toLowerCase()>b.toLowerCase()?1:-1)*(order=="asc"?1:-1);
};


function eXcell_cororo(a) {
    this.base = eXcell_co;
    this.base(a);
    this.editable = false;
    this.edit = function () {
    };
    this.isDisabled = function () {
        return true
    };
}
eXcell_cororo.prototype = new eXcell_co;


function eXcell_rocopy(a) {
    this.base = eXcell_ed;
    this.base(a);
    this.detach = function () {
        this.setValue(this.val);
        return false;
    }
}
eXcell_rocopy.prototype = new eXcell_ed;

function eXcell_corofilter(a) {
    this.base = eXcell_co;
    this.base(a);
    this.editable = false;

    this.edit = function () {
        this.val = this.getValue();
        this.text = this.getText()._dhx_trim();
        var g = this.grid.getPosition(this.cell);
        this.obj = document.createElement("TEXTAREA");
        this.obj.className = "dhx_combo_edit";
        this.obj.style.height = this.cell.offsetHeight - (this.grid.multiLine ? 9 : 4) + "px";
        this.obj.wrap = "soft";
        this.obj.style.textAlign = this.cell.style.textAlign;
        this.obj.onclick = function (o) {
            (o || event).cancelBubble = true;
        };
        this.obj.onmousedown = function (o) {
            (o || event).cancelBubble = true;
        };
        this.obj.value = this.text;
        this.obj.onselectstart = function (o) {
            if (!o) {
                o = event;
            }
            o.cancelBubble = true;
            return true;
        };
        var l = this;
        this.obj.onkeyup = function (s) {
            var r = (s || event).keyCode;
            if (r == 38 || r == 40 || r == 9) {
                return;
            }
            var u = this.readonly ? String.fromCharCode(r) : this.value;
            var v = l.list.options;
            for (var o = 0; o < v.length; o++) {
                if (v[o].text.indexOf(u) == 0) {
                    return (v[o].selected = true);
                }
            }
        };
        this.list = document.createElement("SELECT");
        this.list.className = "dhx_combo_select";
        this.list.style.width = this.cell.offsetWidth + "px";
        this.list.style.left = g[0] + "px";
        this.list.style.top = g[1] + this.cell.offsetHeight + 32 + "px";
        this.list.onclick = function (s) {
            var r = s || window.event;
            var o = r.target || r.srcElement;
            if (o.tagName == "OPTION") {
                o = o.parentNode;
            }
            l.editable = false;
            l.grid.editStop();
            r.cancelBubble = true;
        };
        var c = this.combo.getKeys();
        var h = false;
        var n = 0;
        for (var e = 0; e < c.length; e++) {
            var m = this.combo.get(c[e]);
            this.list.options[this.list.options.length] = new Option(m, c[e]);
            if (c[e] == this.val) {
                n = this.list.options.length - 1;
                h = true;
            }
        }
        if (h == false) {
            this.list.options[this.list.options.length] = new Option(this.text, this.val === null ? "" : this.val);
            n = this.list.options.length - 1;
        }
        document.body.appendChild(this.list);
        this.list.size = "25";
        this.cstate = 1;

        if (this.editable) {
            this.cell.innerHTML = "";
        } else {
            this.obj.style.width = "0px";
            this.obj.style.height = "0px";
        }
        this.cell.appendChild(this.obj);
        this.list.options[n].selected = true;
        if (this.editable) {
            this.obj.focus();
            this.obj.focus();
        }
        if (!this.editable) {
            this.obj.style.visibility = "hidden";
            this.obj.style.position = "absolute";
            this.list.focus();
            this.list.onkeydown = function (o) {
                o = o || window.event;
                l.grid.setActive(true);
                if (o.keyCode < 30) {
                    return l.grid.doKey({ target: l.cell, keyCode: o.keyCode, shiftKey: o.shiftKey, ctrlKey: o.ctrlKey });
                }
            };
        }
        this.filter_input = document.createElement("INPUT");
        this.filter_input.style.height = (this.cell.offsetHeight - 4) + "px";
        this.filter_input.style.width = "100%";
        this.filter_input.style.float = "left";
        this.filter_input.className = "dhx_combo_edit";
        this.cell.appendChild(this.filter_input);
        this.filter_input.focus();
        this.filter_input.onkeyup = function () {
            var filterValue = l.filter_input.value.toLowerCase();
            for (var i = 0; i < l.list.options.length; i++) {
                var option = l.list.options[i];
                var optionText = option.text.toLowerCase();

                if (optionText.indexOf(filterValue) !== -1) {
                    option.style.display = "";
                } else {
                    option.style.display = "none";
                }
            }
        };
        this.filter_input.onclick=function(e){ (e||event).cancelBubble=true;}
    };
}
eXcell_corofilter.prototype = new eXcell_co;

function eXcell_txtcropped(cell) {               // excell name is defined here
    this.base = eXcell_txt;
    this.base(cell);
    if (cell){                                 // default pattern, just copy it
        this.cell = cell;
        this.grid = this.cell.parentNode.grid;
        if(this.cell.val==undefined || this.cell.val==null || this.cell.val=="")
            this.cell.val = "";
    }
    this.getValue = function () {
        return this.cell.val
    };
    this.setValue=function(val){
        if(val!=undefined && val!=null && val!="")
        {
            this.cell.val = val;
            this.setCValue(crop(stripTags(val),20));
            return true;
        }

        val = "";
        this.cell.val = val;
        this.setCValue(val);
    }
}
eXcell_txtcropped.prototype = new eXcell_txt;

function eXcell_txtrocropped(cell) {               // excell name is defined here
    this.base = eXcell_txtcropped;
    this.base(cell);
    this.editable = false;
    if (cell){                                 // default pattern, just copy it
        this.cell = cell;
        this.grid = this.cell.parentNode.grid;
    }
    this.edit = function () {
    };
}
eXcell_txtrocropped.prototype = new eXcell_txtcropped;

/*
PLACE HOLDER
 */
function eXcell_coPH(cell){                  // excell name is defined here
    this.base = eXcell_co;
    this.base(cell);
    if (cell){                                 // default pattern, just copy it
        this.cell = cell;
        this.grid = this.cell.parentNode.grid;
        if(this.cell.placeholder==undefined || this.cell.placeholder==null || this.cell.placeholder=="")
            this.cell.placeholder = "";
        if(this.cell._attrs.placeholder!=undefined && this.cell._attrs.placeholder!=null && this.cell._attrs.placeholder!="")
            this.cell.placeholder = this.cell._attrs.placeholder;
    }
    this.setValue=function(g){
        if (typeof (g) == "object") {
            var e = dhx4.ajax.xpath("./option", g);
            if (e.length) {
                this.cell._combo = new dhtmlXGridComboObject()
            }
            for (var c = 0; c < e.length; c++) {
                this.cell._combo.put(e[c].getAttribute("value"), e[c].firstChild ? e[c].firstChild.data : "")
            }
            g = g.firstChild.data
        }
        if ((g || "").toString()._dhx_trim() == "") {
            g = null
        }
        this.cell.combo_value = g;
        if (g !== null) {
            var a = (this.cell._combo || this.grid.getCombo(this.cell._cellIndex)).get(g);
            this.setCValue(a === null ? g : a, g)
        } else {
            if(this.cell.placeholder!=undefined && this.cell.placeholder!=null && this.cell.placeholder!="")
            {
                var placeholder = this.cell.placeholder;
                this.setCValue(placeholder,"");
            }
            else
                this.setCValue("&nbsp;", g);
        }
    }
    this.setPlaceholder = function (placeholder) {
        if(placeholder!=undefined && placeholder!=null && placeholder!="")
        {
            this.cell.placeholder = placeholder;

            var val = this.getValue();
            if(val==undefined || val==null || val=="")
                this.setValue(val);
        }
    };
}
eXcell_coPH.prototype = new eXcell_co;