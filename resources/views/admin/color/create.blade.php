@extends('admin.layout.app')

@section('content')

<section class="content-header">					
    <div class="container-fluid my-2">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Add color</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{route('color.index')}}" class="btn btn-primary">Back</a>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
</section>
<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="container-fluid">
    <form action="" method="post" id="createColor" name="createColor">
        <div class="card">
            <div class="card-body">								
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name">Name</label>
                            <input type="text" name="name" id="name" class="form-control" placeholder="Name">
                            <p></p>	
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="colorcode">Color code</label>
                            <input type="color" name="colorcode" id="colorcode" class="form-control" placeholder="color">	
                            <p></p>
                        </div>
                    </div>	
						
                </div>
            </div>							
        </div>
        <div class="pb-5 pt-3">
            <button type="submit" class="btn btn-primary">ADD</button>
            <a href="{{route('color.index')}}" class="btn btn-outline-dark ml-3">Cancel</a>
        </div>
    </form>
    </div>
    <!-- /.card -->
</section>

@endsection

@section('customJs')

<script>
    $("#createColor").submit(function(event){
         event.preventDefault();
          var element=$(this);
          $("button[type=submit]").prop('disabled',true);
         $.ajax({
            url:'{{route("color.store")}}',
            type:'post',
            data: element.serializeArray(),
            dataType:'json',
            success:function(response){
                $("button[type=submit]").prop('disabled',false);
                
                if(response["status"]==true){

                    window.location.href="{{route('color.index')}}";

                    $("#name").removeClass('is-invalid')
                    .siblings('p')
                    .removeClass('invalid-feedback').html("");

                    $("#colorcode").removeClass("is-invalid")
                    .siblings('p')
                    .removeClass('invalid-feedback').html("");

                }
                else{
                    var errors = response['errors'];
                   if(errors['name']) {
                    $("#name").addClass('is-invalid')
                    .siblings('p')
                    .addClass('invalid-feedback').html(errors['name']);
                    }
                   else{
                    $("#name").removeClass('is-invalid')
                    .siblings('p')
                    .removeClass('invalid-feedback').html("");
                   }

                   if(errors['colorcode']) {
                    $("#colorcode").addClass('is-invalid')
                    .siblings('p')
                    .addClass('invalid-feedback').html(errors['colorcode']);
                }
                else{
                    $("#colorcode").removeClass("is-invalid")
                    .siblings('p')
                    .removeClass('invalid-feedback').html("");
                }

                }

            },
            error:function(jqXHR,exception){
                console.log("Something went wrong");
            }
         });
         
    });

    // $("#name").change(function(){
    //     element=$(this);
    //     $("button[type=submit]").prop('disabled',true);
      
    //     $.ajax({
    //         url:'{{route("getslug")}}',
    //         type:'get',
    //         data: {title:element.val()},
    //         dataType:'json',
    //         success:function(response){
    //             $("button[type=submit]").prop('disabled',false);
    //         if(response["status"]==true){
    //             $("#slug").val(response["slug"]);

    //         }
    //     }
    //     });

    // });
   
</script>
@endsection

