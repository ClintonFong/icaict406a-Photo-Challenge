//
// Created by Clinton Fong
// 
//---------------------------------------------------------------------------------------------
// General 
//
//---------------------------------------------------------------------------------------------

//---------------------------------------------------------------------------------------------
// daysInMonth
//---------------------------------------------------------------------------------------------
function daysInMonth( month, year ) 
{
    //Month is 1 based
    return new Date(year, month, 0).getDate();

} // daysInMonth

//---------------------------------------------------------------------------------------------
// isValidNormalCharKey
//
// Description: allows only valid normal character keys input 0-9, a-z, and A-Z
//---------------------------------------------------------------------------------------------
function isValidNormalCharKey(evt)
{
    var charCode = (evt.which) ? evt.which : event.keyCode;

    if ((charCode == 8) ||                          // backspace
          (charCode == 32) ||                          // space
         ((charCode >= 48) && (charCode <= 57)) ||     // 0-9
         ((charCode >= 65) && (charCode <= 90)) ||     // A-Z
         ((charCode >= 97) && (charCode <= 122))       // a-z
         )
    {
        return true;
    }

    return false;

} // isValidNormalCharKey


//---------------------------------------------------------------------------------------------
// isIntegerKey
//
// Description: allows only integer input
//---------------------------------------------------------------------------------------------
function isIntegerKey(evt)
{
    var charCode = (evt.which) ? evt.which : event.keyCode;

    if ((charCode != 8) &&
         ((charCode < 48) || (charCode > 57)))
    {
        return false;
    }
    return true;

} // isIntegerKey

//---------------------------------------------------------------------------------------------
// isFloatKey
//
// Description: allows only float input
//---------------------------------------------------------------------------------------------
function isFloatKey(evt)
{
    var charCode = (evt.which) ? evt.which : event.keyCode;

    // check for extra decimal point
    //
    if (charCode == 46) // decimal point
    {
        if (!(evt.target.value.indexOf('.') === -1))
        {
            return false;   // already found a decimal 
        }
    }

    // check if valid keystroke
    //
    if ((charCode != 46) && (charCode > 31) &&
         ((charCode < 48) || (charCode > 57)))
    {
        return false;
    }

    return true;

} // isFloatKey



//---------------------------------------------------------------------------------------------
// isMoneyKey
//
// Description: allows only float input
//---------------------------------------------------------------------------------------------
function isMoneyKey(evt)
{
    var charCode = (evt.which) ? evt.which : event.keyCode;

    // check for extra decimal point
    //
    if (charCode == 46) // decimal point
    {
        if (!(evt.target.value.indexOf('.') === -1))
        {
            return false;   // already found a decimal 
        }
    }

    // check if valid keystroke
    //
    if ((charCode != 46) && (charCode > 31) &&
         ((charCode < 48) || (charCode > 57)))
    {
        return false;
    }

    // check for no more than 2 decimal places
    //
    if (charCode != 8) // only valid key here is backspace
    {
        integer = evt.target.value.split('.')[0];
        mantissa = evt.target.value.split('.')[1];

        if (typeof mantissa === 'undefined') { mantissa = ''; }
        if (mantissa.length >= 2) { return false; }  // already exceeded number of decimal places
    }

    return true;

} // isMoneyKey



//---------------------------------------------------------------------------------------------
// isPhoneNumberKey
//
// Description: allows only phone number input
//---------------------------------------------------------------------------------------------
function isPhoneNumberKey(evt)
{
    var charCode = (evt.which) ? evt.which : event.keyCode;

    if (charCode != 8) // only valid key here is backspace
    {
        if ((charCode != 40) && (charCode > 31) && (charCode != 41) && (charCode != 43) && (charCode != 32) &&
             ((charCode < 48) || (charCode > 57)))
        {
            return false;
        }
    }
    return true;

} // isPhoneNumberKey

//---------------------------------------------------------------------------------------------
// isPhoneExtKey
//
// Description: allows only phone number input
//---------------------------------------------------------------------------------------------
function isPhoneExtKey(evt)
{
    var charCode = (evt.which) ? evt.which : event.keyCode;

    if (charCode != 8) // only valid key here is backspace
    {

        if ((charCode != 40) && (charCode > 31) && (charCode != 41) && (charCode != 43) && (charCode != 32) &&
             ((charCode < 48) || (charCode > 57)))
        {
            return false;
        }

        // check if 5 or less characters
        if (evt.target.value.length >= PHONE_EXT_MAX_LENGTH)
        {
            return false;
        }
    }

    return true;

} // isPhoneExtKey

//---------------------------------------------------------------------------------------------
function truncatePhoneExt(phoneNo)
{
    return phoneNo.substring(0, Math.min(PHONE_EXT_MAX_LENGTH, phoneNo.length));

} // truncatePhoneExt

//---------------------------------------------------------------------------------------------
// Checks
//---------------------------------------
function isValidEmail(email)
{
    var regex = /^([a-zA-Z0-9_.+-])+\@([a-zA-Z0-9-])+(\.([a-zA-Z0-9-])+)*(\.[a-zA-Z0-9]{2,4})*$/;
    return regex.test(email);
    //return true;

} // isValidEmail


//---------------------------------------
function isPasswordSecureEnough(password)
{
    var bSecureEnough = true;

    if (password.length < 7) { bSecureEnough = false; }
    else
    {
        var bHasNumber = false;
        var bHasLetter = false;

        if (password.match(/[0-9]/)) { bHasNumber = true; }
        if (password.match(/[a-z]/i)) { bHasLetter = true; }
        bSecureEnough = (bHasNumber && bHasLetter);
    }
    return bSecureEnough;

} // isPasswordSecureEnough



//---------------------------------------------------------------------------------------------
// XML with Ajax data
//---------------------------------------------------------------------------------------------

//---------------------------------------------------------------------------------------------
function stripStatusFromAjaxData(data)
{
    return getXMLDoc(data).getElementsByTagName('status')[0].childNodes[0].nodeValue;

} // stripStatusFromAjaxData

//---------------------------------------------------------------------------------------------
function stripMessageFromAjaxData(data)
{
    return getXMLDoc(data).getElementsByTagName('message')[0].childNodes[0].nodeValue;

} // stripMessageFromAjaxData

//---------------------------------------------------------------------------------------------
function stripDataFromAjaxData(data)
{
    return getXMLDoc(data).getElementsByTagName('data')[0].childNodes[0].nodeValue;

} // stripDataFromAjaxData

//---------------------------------------------------------------------------------------------
function stripIDFromAjaxData(data)
{
    return (typeof getXMLDoc(data).getElementsByTagName('id')[0] == 'undefined') ? "" : getXMLDoc(data).getElementsByTagName('id')[0].childNodes[0].nodeValue;

} // stripIDFromAjaxData

//---------------------------------------------------------------------------------------------
// getXMLDoc
//
// Description: Parses and returns the XML Document for a given XML string
//              Extracted some of the code from http://www.w3schools.com/xml/xml_parser.asp
//---------------------------------------------------------------------------------------------
function getXMLDoc(data)
{
    var xmlDoc;

    if (window.DOMParser)
    {
        var parser = new DOMParser();
        xmlDoc = parser.parseFromString(data, 'text/xml');
    }
    else // Internet Explorer
    {
        xmlDoc = new ActiveXObject('Microsoft.XMLDOM');
        xmlDoc.async = false;
        xmlDoc.loadXML(data);
    }
    return xmlDoc;

} // getXMLDoc

//-------------------------------------------------------------------------------------------------------
// xmlToString - taken from http://stackoverflow.com/questions/6507293/convert-xml-to-string-with-jquery
//
// Description: converts xml data to a string
//-------------------------------------------------------------------------------------------------------
function xmlToString(xmlData)
{

    var xmlString;
    //IE
    if (window.ActiveXObject)
    {
        xmlString = xmlData.xml;
    }
    else // code for Mozilla, Firefox, Opera, etc.
    {
        xmlString = (new XMLSerializer()).serializeToString(xmlData);
    }
    return xmlString;

} // xmlToString

// --------------------------------------------------------------------------
// --------------------------------------------------------------------------
// Cookies - code taken from http://stackoverflow.com/questions/1458724/how-to-set-unset-cookie-with-jquery
//

function createCookie(name, value, days) 
{
    var expires;

    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toGMTString();
    } else {
        expires = "";
    }
    document.cookie = escape(name) + "=" + escape(value) + expires + "; path=/";

} // createCookie

function readCookie(name) 
{
    var nameEQ = escape(name) + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) 
    {
        var c = ca[i];
        while (c.charAt(0) === ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) === 0) return unescape(c.substring(nameEQ.length, c.length));
    }
    return null;

} // readCookie

function eraseCookie(name) 
{
    createCookie(name, "", -1);

} // eraseCookie


