$(document).ready(function () {
  $.ajax({
    url: "../query.php",
    data: { x: "1" },
    type: "GET",
    success: function (data) {
      console.log(data);
    },
  });
});
