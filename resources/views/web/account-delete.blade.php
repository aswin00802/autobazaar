<!DOCTYPE html>
<html lang="en">
<head>
  <title>AutoBazaar Account Delete</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <style>
   .cus-card{
    width: 75%;
    margin: 0 auto;
    border: 1px solid #d3caca;
    box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    margin-top: 10%;
    padding: 5%;
   }

  </style>
</head>
<body>

<div class="container">
 <div class="card cus-card">
  <h2 class="text-center fw-bold">Account Delete Form</h2>
  <form action="{{ route('account-delete-store') }}" method="post">
   @csrf
    <div class="form-group">
      <label for="phone">Phone:</label>
      <input type="number" class="form-control" id="phone" placeholder="Enter Phone Number" name="phone">
    </div> 
   
    <button type="submit" class="btn btn-danger mx-auto">Delete Account</button>
  </form>
</div>
</div>

<!-- Toastr CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<!-- jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
 // Display Toastr notifications for session messages
 $(document).ready(function () {
     @if(session('success'))
         toastr.success("{{ session('success') }}");
         <?php session()->forget('success'); ?>
     @else if(session('error'))
         toastr.error("{{ session('error') }}");
         <?php session()->forget('success'); ?>
     @endif
 });
</script>


</body>
</html>
