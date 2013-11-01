/**
 * Created with JetBrains PhpStorm.
 * User: vadimdez
 * Date: 10/20/13
 * Time: 3:25 PM
 * To change this template use File | Settings | File Templates.
 */


$(function() {

    var availableTags = [
        "WhiteRussian"
    ];
    function split( val ) {
        return val.split( /,\s*/ );
    }
    function extractLast( term ) {
        return split( term ).pop();
    }

    $( "#tags" )
        // don't navigate away from the field on tab when selecting an item
        .bind( "keydown", function( event ) {
            if ( event.keyCode === $.ui.keyCode.TAB &&
                $( this ).data( "ui-autocomplete" ).menu.active ) {
                event.preventDefault();
            }
        })
        .autocomplete({
            minLength: 0,
            source: function( request, response ) {
                // delegate back to autocomplete, but extract the last term
                response( $.ui.autocomplete.filter(
                    availableTags, extractLast( request.term ) ) );
            },
            focus: function() {
                // prevent value inserted on focus
                return false;
            },
            select: function( event, ui ) {
                var terms = split( this.value );
                // remove the current input
                terms.pop();
                // add the selected item
                terms.push( ui.item.value );
                // add placeholder to get the comma-and-space at the end
                terms.push( "" );
                this.value = terms.join( ", " );
                $("#test").html("").append($("#tags").val());
                return false;
            }
        });
});
//
//    $( "#tags" ).autocomplete({
//        minLength: 0,
//        source: availableTags,
//        appendTo: "#test",
//        focus: function( event, ui ) {
//            $( "#tags" ).val( ui.item.label );
//            return false;
//        },
//        select: function( event, ui ) {
//            $( "#tags" ).val( ui.item.label );
//            $("#test").html("").append("asd");
//            return false;
//        }
//    })
//});