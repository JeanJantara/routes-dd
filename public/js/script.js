$(function(){
    
    $("#shortest-path-form").on("submit", function(e) {
        e.preventDefault();
        $("#loadspinner").removeClass("d-none");
        $("#messages").text("");
        $.post("/shortest-path", $(this).serialize(), function(data){
            $("#loadspinner").addClass("d-none");
            data = $.parseJSON(data);
            if(!data.error) {
                var msg = "Shortest Path: ";
                var total = 0;
                var shortest = data.path;
                while(shortest.length > 0) {
                    var path = shortest.shift();
                    total += path[Object.keys(path)[0]];
                    if(shortest.length != 0) {
                        msg += Object.keys(path)[0] + " -> "
                    } else {
                        msg += Object.keys(path)[0];
                    }
                }
                msg += "<br /> Total: " + total;
            } else {
                var msg = data.msg;
            }
            $("#messages").html(msg);
        });
    });

    $("#new-city-form").on("submit", function(e) {
        e.preventDefault();
        $("#loadspinner").removeClass("d-none");
        $("#messages").text("");
        $.post("/city/new", $(this).serialize(), function(data){            
            $("#loadspinner").addClass("d-none");
            data = $.parseJSON(data);
            if(!data.error) {
                $("#tbody-cities").prepend(
                    "<tr><td>" + data.city.name + "</td><td>" + data.city.lat + "</td><td>" + data.city.long + "</td></tr>"
                );
            }
            $("#messages").text(data.msg);
        });
    });

});