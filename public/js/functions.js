/**
 * Created with JetBrains PhpStorm.
 * User: vadimdez
 * Date: 10/19/13
 * Time: 2:13 PM
 * To change this template use File | Settings | File Templates.
 */
$(document).ready(function()
{
    var param1  = 10;
    var offsetCocktails  = 10;
    var offsetCategories = 10;
    var offsetIngridients= 10;

    $("#loadMoreCocktails").click(function() {
        jQuery.ajax({
            url: 'http://localhost/cocktails/public/admin/morecocktails',
            type: 'POST',
            data: {limit: param1, offset:offsetCocktails },
            dataType: "json",
            success: function(result){


                jQuery.each( result, function(i,val) {
                    jQuery.each( val, function(j,val_j) {

                    $("#cocktails").append("<tr><td><a href='cocktail/details/" + val_j.idCocktail + "'>" + val_j.cocktailName + "</a></td><td><a href='admin/edit/"+val_j.idCocktail+"'>Edit</a> <a href='admin/ingridients/"+val_j.idCocktail+"'>Ingridients</a> <a href='admin/delete/"+val_j.idCocktail+"'>Delete</a></td></tr>");
                    });
                });

                offsetCocktails += param1;
            }
    });
});

    $("#loadMoreCategories").click(function() {
        jQuery.ajax({
            url: 'http://localhost/cocktails/public/admin/morecategories',
            type: 'POST',
            data: {limit: param1, offset:offsetCategories },
            dataType: "json",
            success: function(result){


                jQuery.each( result, function(i,val) {
                    jQuery.each( val, function(j,val_j) {

                        $("#categories").append("<tr><td>" + val_j.categoryName + "</td><td><a href='editcategory/"+val_j.idCategory+"'>Edit</a> <a href='deletecategory/"+val_j.idCategory+"'>Delete</a></td></tr>");
                    });
                });

                offsetCategories += param1;
            }


        });
    });

    $("#loadMoreIngridients").click(function() {
        jQuery.ajax({
            url: 'http://localhost/cocktails/public/admin/moreingridients',
            type: 'POST',
            data: {limit: param1, offset:offsetIngridients },
            dataType: "json",
            success: function(result){


                jQuery.each( result, function(i,val) {
                    jQuery.each( val, function(j,val_j) {

                        $("#ingridients").append("<tr><td><a href='/cocktails/public/ingridients/details/" + val_j.idIngridient + "'>" + val_j.ingridientName + "</a></td><td><a href='/cocktails/public/ingridients/edit/"+val_j.idIngridient+"'>Edit</a> <a href='/cocktails/public/ingridients/delete/"+val_j.idIngridient+"'>Delete</a></td></tr>");
                    });
                });

                offsetIngridients += param1;
            }


        });
    });

});

