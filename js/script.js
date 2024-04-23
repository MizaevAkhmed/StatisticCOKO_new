$(document).ready(function () {

  $("#select_EGE_OGE").on('change', function(e) {
    e.preventDefault();
    let ege_oge_value = $(this).val();
    $.ajax({
        url: "get_tests.php",
        data: { 'ege_oge': ege_oge_value }, // обратите внимание на 'ege_oge'
        type: "GET",
        success: function (result) {
          console.log(result);
          // Update the DOM with received data
          $("#projectTestSelect").html(result);
        },
      });
  });

  $('input[name="exampleRadios"]').change(function(){
    if ($('#exampleRadios2').is(':checked')) {
      $('#levels').css('display', 'block');
    } else {
      $('#levels').css('display', 'none');
    }
  });

  $('#generateBtn').click(function(e){
    e.preventDefault();
    let projectTestId = $('#projectTestSelect').val();
    let projectPass = $('#projectPass').val();
    let levels = $('#projectLevels').val();
    $.ajax({
        url: "../query.php",
        data: { projectTestId: projectTestId, pass: projectPass },
        type: "POST",
        success: function (data) {
          console.log(data);
        },
      });
  })
});
