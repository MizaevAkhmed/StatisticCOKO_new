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
    if ($('#exampleRadios1').is(':checked')) {
      var option = $(this).val();
      $('#passing-score').css('display', 'block');
    } else {
      $('#passing-score').css('display', 'none');
    }
  });

  $('input[name="exampleRadios"]').change(function(){
    if ($('#exampleRadios2').is(':checked')) {
      var option = $(this).val();
      $('#levels').css('display', 'block');
    } else {
      $('#levels').css('display', 'none');
    }
  });

  $('#generateBtn').click(function(e){
    e.preventDefault();
    let projectTestId = $('#projectTestSelect').val();
    let projectPass = $('#projectPass').val();
    // let levels = 1;
    let lowLevelMin = $('#low-level-min').val();
    let lowLevelMax = $('#low-level-max').val();
    let baseLevelMin = $('#base-level-min').val();
    let baseLevelMax = $('#base-level-max').val();
    let aboveBaseLevelMin = $('#above-base-level-min').val();
    let aboveBaseLevelMax = $('#above-base-level-max').val();
    let highLevelMin = $('#high-level-min').val();

    $.ajax({
        url: "../query.php",
        data: { 
          projectTestId: projectTestId, 
          pass: projectPass,
          levels: levels,
          lowLevelMin: lowLevelMin, 
          lowLevelMax: lowLevelMax, 
          baseLevelMin: baseLevelMin,
          baseLevelMax: baseLevelMax,
          aboveBaseLevelMin: aboveBaseLevelMin,
          aboveBaseLevelMax: aboveBaseLevelMax,
          highLevelMin: highLevelMin
        },
        type: "POST",
        success: function (data) {
          console.log(data);
        },
      });
  })
});
