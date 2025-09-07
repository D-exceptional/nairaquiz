import { displaySuccess, displayInfo } from "../scripts/export.js";

document.addEventListener("DOMContentLoaded", function () {
  function updateCount() {
    //Get count
    const counter = Number($("tbody").children("tr").length);
    $(".col-sm-6 h1").empty().html(`<b>Question (${counter})</b>`);
  }

  //Delete a question
  function deleteQuestion(id) {
    const promptMessage = confirm(
      "Are you sure to delete question: " + '" ' + id + ' "' + " ?"
    );
    if (promptMessage === true) {
      $.ajax({
        type: "POST",
        url: "../server/question.php",
        data: { id: id },
        dataType: "json",
        success: function (response) {
          for (var key in response) {
            if (Object.hasOwnProperty.call(response, key)) {
              const content = response.Info;
              if (content === "Question deleted successfully") {
                $(".rows").each(function () {
                  if ($(this).attr("id") === id) {
                    $(this).remove();
                    setTimeout(() => {
                      updateCount();
                    }, 1000);
                  }
                });
                displaySuccess(content);
              } else {
                displayInfo(content);
              }
            }
          }
        },
        error: function (e) {
          displayInfo(e.responseText);
        },
      });
    } else {
      $("tbody").css({ opacity: "1" });
    }
  }

  $(".rows .btn.btn-danger").each(function (index, el) {
    $(el).on("click", function () {
      const id = $(el).parent().parent().attr("id");
      deleteQuestion(id);
    });
  });

  $("#open-overlay").on("click", function () {
    $("#details-overlay").css({ display: "flex" });
  });

  $("#close-view").on("click", function () {
    $("#details-overlay").css({ display: "none" });
  });

  //Indent all inner child navs
  $(".nav-sidebar").addClass("nav-child-indent");

  //Search function
  $("#page-search").on("keyup", function () {
    let searchValue = $(this).val();
    if (searchValue !== "") {
      $(".rows").each(function (index, el) {
        if (
          $(el).find("td").text().toLowerCase().includes(searchValue)
        ) {
          $(el).css({ display: "table-row" });
        } else {
          $(el).css({ display: "none" });
        }
      });
    } else {
      $(".rows").each(function (index, el) {
        if ($(el).css("display") === "none") {
          $(el).css({ display: "table-row" });
        }
      });
    }
  });

  function updateContent(id, type, value) {
    if (!id || !type || !value) {
      displayInfo("Missing parameters");
      return;
    }
    // Prepare the data object to send
    const dataToSend = {
      id: id,
      type: type,
      value: value,
    };
    //Send data to server to update question details
    $.ajax({
      type: "POST",
      url: "../server/update-question.php",
      data: dataToSend,
      dataType: "json",
      success: function (response) {
        for (const key in response) {
          if (Object.hasOwnProperty.call(response, key)) {
            const content = response.Info;
            if (content === "Detail updated") {
              $(".rows").each(function (index, el) {
                if ($(el).attr("id") === id) {
                  $(el).find(`.${type}`).empty().text(value);
                }
              });
              //Show response
              if (type === "question") {
                displaySuccess("Question updated");
              } else {
                displaySuccess("Option updated");
              }
            } else {
              //displayInfo(content);
              displayInfo(content);
            }
          }
        }
      },
      error: function (e) {
        displayInfo("Error connecting to server");
      },
    });
  }

  function getQuestions(count) {
    $("tbody").empty();
    $("#loading-overlay").css({ display: "flex" });
    //get and update with number of questions selected by admin
    $.ajax({
      type: "GET",
      url: `../server/fetch-questions.php?count=${count}`,
      dataType: "json",
      processData: false,
      contentType: false,
      cache: false,
      success: function (response) {
        for (const key in response) {
          if (Object.hasOwnProperty.call(response, key)) {
            const content = response.Info;
            if (content === "Questions fetched") {
              //Notify admin
              $("#loading-overlay").css({ display: "none" });
              displaySuccess(`${count} questions fetched successfully`);
              const questions = response.questions;
              $.each(questions, function (index, question) {
                let list = `<tr class='rows' id='${question.id}'>
                                    <td class='question' tabindex='0'>${question.question}</td>
                                    <td class='option_one' tabindex='0'>${question.opt1}</td>
                                    <td class='option_two' tabindex='0'>${question.opt2}</td>
                                    <td class='option_three' tabindex='0'>${question.opt3}</td>
                                    <td class='option_four' tabindex='0'>${question.opt4}</td>
                                    <td>
                                      <button class='btn btn-danger btn-sm'>Delete</button>
                                    </td>
                                  </tr>
                                `;
                $("tbody").append(list);
              });
            } else {
              //displayInfo(content);
              $("#loading-overlay").css({ display: "none" });
              displayInfo(content);
            }
          }
        }
        //Edit dynamically loaded questions
        $(".rows td").each(function (index, el) {
          const id = $(el).parent().attr("id");
          const type = $(el).attr("class");
          const initialContent = $(el).text();
          //Start
          $(el)
            .on("focus", function () {
              if (!$(el).find("button").length) {
                $(el).attr("contenteditable", true);
              } 
            })
            .on("blur", function () {
              if (!$(el).find("button").length) {
               $(el).attr("contenteditable", false);
               const currentContent = $(el).text();
               //Check for changes
               if (currentContent !== initialContent) {
                 //Update content
                 updateContent(id, type, currentContent);
               } 
              } 
            });
        });
        //Delete dynamically loaded questions
        $(".rows .btn.btn-danger").each(function (index, el) {
          $(el).on("click", function () {
            const id = $(el).parent().parent().attr("id");
            deleteQuestion(id);
          });
        });
      },
      error: function (e) {
        displayInfo("Error connecting to server");
      },
    });
  }

  //Filter number of questions on the pag
  $("#filter").on("change", function () {
    const count = $(this).val();
    getQuestions(count);
  });

  //Edit questions and answers by double-clicking on the
  $(".rows td").each(function (index, el) {
    const id = $(el).parent().attr("id");
    const type = $(el).attr("class");
    const initialContent = $(el).text();
    //Start
    $(el)
      .on("focus", function () {
        if (!$(el).find("button").length) {
          $(el).attr("contenteditable", true);
        } 
      })
      .on("blur", function () {
        if (!$(el).find("button").length) {
          $(el).attr("contenteditable", false);
          const currentContent = $(el).text();
          //Check for changes
          if (currentContent !== initialContent) {
            //Update content
            updateContent(id, type, currentContent);
          } 
        }
      });
  });

  /************* Main Upload Logic ************/
  // Setting the worker source for PDF.js
  pdfjsLib.GlobalWorkerOptions.workerSrc =
    "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.11.338/pdf.worker.min.js";

  async function handleFileSelect(event) {
    const file = document.getElementById("file").files[0];
    const filename = file.name;
    if (file) {
      try {
        const text = await extractTextFromPDF(file);
        const questions = [];
        const questionBlocks = text.split(/\*\*\s*\d+\.\s/).slice(1);

        questionBlocks.forEach((block) => {
          const parts = block
            .split(/\s+A\)\s|B\)\s|C\)\s|D\)\s/)
            .map((part) => part.trim());
          const question = parts[0].replace(/\*\*/, "").trim(); // Remove '**' and trim
          //const question = parts[0];
          const answers = {
            a: parts[1].replace(" (Correct)", ""),
            b: parts[2].replace(" (Correct)", ""),
            c: parts[3].replace(" (Correct)", ""),
            d: parts[4].replace(" (Correct)", ""),
          };

          let correctAnswer = "";
          if (parts[1].includes("(Correct)")) correctAnswer = "a";
          else if (parts[2].includes("(Correct)")) correctAnswer = "b";
          else if (parts[3].includes("(Correct)")) correctAnswer = "c";
          else if (parts[4].includes("(Correct)")) correctAnswer = "d";

          questions.push({ question, answers, correctAnswer });
        });

        console.log(filename, questions);
        //Prepare data
        const payload = JSON.stringify(questions);
        await sendQuestions(payload, filename);
      } catch (error) {
        console.error("Error handling file:", error);
      }
    }
  }

  async function extractTextFromPDF(file) {
    try {
      const pdfData = await file.arrayBuffer();
      const pdf = await pdfjsLib.getDocument({ data: pdfData }).promise;
      let textContent = "";

      for (let i = 1; i <= pdf.numPages; i++) {
        const page = await pdf.getPage(i);
        const text = await page.getTextContent();
        textContent += text.items.map((item) => item.str).join(" ") + "\n";
      }

      return textContent;
    } catch (error) {
      console.error("Error extracting text from PDF:", error);
      throw error;
    }
  }

  async function sendQuestions(data, name) {
    try {
      $.ajax({
        type: "POST",
        url: "../server/upload.php",
        data: { questions: data, filename: name },
        dataType: "json",
        success: function (response) {
          for (const key in response) {
            if (Object.hasOwnProperty.call(response, key)) {
              const content = response.Info;
              if (content === "Questions uploaded successfully") {
                displaySuccess(response.details);
                setTimeout(function () {
                  window.location.reload();
                }, 1500);
              } else {
                displayInfo(content);
              }
            }
          }
        },
        error: function (e) {
          displayInfo(e.responseText);
        },
      });
    } catch (error) {
      console.error("Error sending questions:", error);
    }
  }

  //Toggle Profile Preview
  $("#file").on("change", function () {
    handleFileSelect(this);
  });

  //Initialize Profile Submit
  $("#upload").on("click", function () {
    $("#file").click();
  });

  // Example of attaching the file select event
  /*document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('file').addEventListener('change', handleFileSelect);
    });
    */
});
