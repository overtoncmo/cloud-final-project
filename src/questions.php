<?php
    require_once("conn.php");
    require_once("questions-map.php");

    $query = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='test_db' AND TABLE_NAME='questions' LIMIT 50;";
    try
        {
        $prepared_stmt = $dbo->prepare($query);
        $prepared_stmt->execute();
        $result = $prepared_stmt->fetchAll();
    }
    catch (PDOException $ex)
    { 
      echo $sql . "<br>" . $error->getMessage();
    }
?>


<!DOCTYPE html>
<html>
        <head>
                <meta name="google" content="notranslate">
                <meta name="pragma" content="no-cache" />
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

                <script>
                        $(document).ready(function(){
                                $(".answer-choice").click(function(){
                                        let answer = $(this).text();
                                        let question = $(this).closest(".survey-item").find(".question").text();
                                        alert(`Clicked on answer: \"${answer}\" for question \"${question}\"`);
                                });

                        });
                </script>
        </head>

    <body>
        <div class="title">
                        <h1 class="display-3"> Big Five Personality Comparison </h1>
                </div>
                <div class="content">


            <?php if ($result && $prepared_stmt->rowCount() > 0) { 
                foreach ($result as $row) { ?>

                <div class="survey-item">
                    <div class="question-container row">
                        <h3 class="display-9 question"><?php echo $question_text[$row['COLUMN_NAME']] ?></h3>
                    </div>
                    <div class="answer-container row">
                        <div class="col answer-choice">Strongly Disagree</div>
                        <div class="col answer-choice">Disagree</div>
                        <div class="col answer-choice">Neutral</div>
                        <div class="col answer-choice">Agree</div>
                        <div class="col answer-choice">Strongly Agree</div>
                    </div>
                </div>
            <?php }
            } ?>

                <style>
                        .content {
                                align-items: center;
                                width: 60%;
                                margin: 0 auto;
                                position: relative;
                                text-align: center;
                        }

                        .question-container {
                                /* text-align: center; */
                                margin: 20px 0;
                        }
                        .answer-container {
                                /* text-align: center; */
                                margin: 20px 0;
                        }

                        .answer-choice {
                                border: 1px solid rgb(33, 37, 41);
                                padding: 10px;
                        }

            .title {
                margin: 50px;
                text-align: center;
            }

                </style>
        </body>

</html>