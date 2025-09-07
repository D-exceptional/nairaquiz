//Get count
const counter = $("tbody").children('tr').length;

$(".col-sm-6 h1")
  .empty()
  .html(
    "<b>Courses</b> " +
      "(" +
         counter
        +
      ")"
  );