<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('ROOT', './');

session_start();

require_once(ROOT.'lib/autoload.php');
require_once(ROOT.'vendor/autoload.php');

?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <title>Test</title>
  </head>
  <body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="./">Home</a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="./list">liste</a>
            </li>
            <!-- <li class="nav-item">
                <a class="nav-link" href="./import">import</a>
            </li> -->
            </ul>
        </div>
    </nav>
    <table class="table table-sm">
        <thead>
            <tr>
                <th scope="col">email</th>
                <th scope="col">action <button type="button" class="btn btn-primary btn-sm btnadd" data-toggle="modal" data-target="#exampleModal">New</button></th>
            </tr>
        </thead>
        <tbody class="liste">
           <tr><td colspan="2">LOADING</td></tr>
        </tbody>
    </table>
    <nav aria-label="Page navigation example">
        <ul class="pagination">
            <li class="page-item" page="0">
                <a class="page-link" href="#" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                    <span class="sr-only">Previous</span>
                </a>
            </li>
            <li class="page-item" page="2">
                <a class="page-link" href="#" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                    <span class="sr-only">Next</span>
                </a>
            </li>
        </ul>
    </nav>
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form>
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">New message</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input id="id" type="hidden" value="idmembre">
                        <div class="form-group">
                            <label for="email_address" class="col-form-label">email_address:</label>
                            <input type="text" class="form-control" id="email_address">
                        </div>
                        <div class="form-group">
                            <label for="FNAME" class="col-form-label">FNAME:</label>
                            <input type="text" class="form-control" id="FNAME">
                        </div>
                        <div class="form-group">
                            <label for="LNAME" class="col-form-label">LNAME:</label>
                            <input type="text" class="form-control" id="LNAME">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">modifier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  </body>
  <script language="javascript">
    var liste = [];
    var offset=0;
    $(document).ready(function(){
        getmembres(offset);
        $("form").submit(function (event) {
            var formData = {
                id: $("#id").val(),
                email_address: $("#email_address").val(),
                FNAME: $("#FNAME").val(),
                LNAME: $("#LNAME").val()
            };
            if($("#id").val()=="new"){
                $.ajax({
                    type: "POST",
                    url: "./users",
                    data: formData,
                    dataType: "json",
                    encode: true,
                }).done(function (data) {
                    console.log(data);
                });
            }else{
                $.ajax({
                    type: "POST",
                    url: "./users",
                    data: formData,
                    dataType: "json",
                    encode: true,
                    headers: {"X-HTTP-Method-Override": "PUT"}
                }).done(function (data) {
                    console.log(data);
                });
            }
            getmembres(offset);
            $('#exampleModal').modal('hide');
            event.preventDefault();
        });
    });
    function getmembres(offset){
        var formData = {
            offset: offset
        };
        $.ajax({
            type: "GET",
            url: "./users",
            data: formData,
            dataType: "json",
            encode: true
        }).done(function( data ) {
            let nbritems = data.total_items;
            let nbrpages = Math.ceil(data.total_items/10);
            liste = data;
            $( ".liste" ).empty();
            $.each( data.liste, function( i, item ) {
                const btn = '<div class="btn-group btn-group-sm" role="group" aria-label="Basic example"><button type="button" class="btn btn-primary btnupd" data-toggle="modal" data-target="#exampleModal">update</button><button type="button" class="btn btn-danger btndel">delete</button></div>';
                $( '<tr seq="'+item.seq+'"><td>'+item.email_address+'</td><td>'+btn+'</td></tr>' ).appendTo( ".liste" );
            });
        });
    }
    
    $('table').on('click','.btnupd', function(event){
        const seq = $(this).parents('tr').attr('seq');
        $('#exampleModal').on('show.bs.modal', function (event) {
            var modal = $(this);
            const id = liste.liste[seq].id;
            const LNAME = liste.liste[seq].LNAME;
            const FNAME = liste.liste[seq].FNAME;
            const email_address = liste.liste[seq].email_address;
            modal.find('.modal-title').text(FNAME+' '+LNAME);
            modal.find('.modal-body input#id').val(id);
            modal.find('.modal-body input#FNAME').val(FNAME);
            modal.find('.modal-body input#LNAME').val(LNAME);
            modal.find('.modal-body input#email_address').val(email_address);
        })

    });
    $('table').on('click','.btndel', function(event){
        const seq = $(this).parents('tr').attr('seq');
        var r = confirm("supprimer");
        if (r == true) {
            const email_address = liste.liste[seq].email_address;
            var formData = {
                email_address: email_address
            };
            $.ajax({
                type: "POST",
                url: "./users",
                data: formData,
                dataType: "json",
                encode: true,
                headers: {"X-HTTP-Method-Override": "DELETE"}
            }).done(function (data) {
                console.log(data);
                getmembres(offset);
            });
        }
    });
    $('table').on('click','.btnadd', function(event){
        $('#exampleModal').on('show.bs.modal', function (event) {
            var modal = $(this);
            const id = "new";
            const LNAME = "";
            const FNAME = "";
            const email_address = "";
            modal.find('.modal-title').text('Nouveau membre');
            modal.find('.modal-body input#id').val(id);
            modal.find('.modal-body input#FNAME').val(FNAME);
            modal.find('.modal-body input#LNAME').val(LNAME);
            modal.find('.modal-body input#email_address').val(email_address);
        })
    });
    $('.pagination').on('click','li', function(event){
        //const seq = $(this).parent().css( "background-color", "red" );
        let page = parseInt($(this).attr('page'));
        offset = parseInt((page-1)*10);
        getmembres(offset);
        const seq = $(this).attr('seq');

        $( ".pagination li" ).each(function( index ) {
            if(index == 0){
                $( this ).attr('page',page-1);
            }else{
                $( this ).attr('page',page+1);
            }
        });
        event.preventDefault();
    });
  </script>
</html>