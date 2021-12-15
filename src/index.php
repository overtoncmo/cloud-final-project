<?php
    require_once("conn.php");
    require_once("questions-map.php");

    // Get the column names (keys in the question map, e.g. "EXT1")
    $query = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='big_five_db' AND TABLE_NAME='responses' LIMIT 50;";
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
		<link href="./style.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

		<script>
            function sum( obj ) {
                var sum = 0;
                for( var el in obj ) {
                    if( obj.hasOwnProperty( el ) ) {
                    sum += parseFloat( obj[el] );
                    }
                }
                return sum;
            }


			$(document).ready(function(){

                $("#test-results").hide();
                $("#incomplete-warning").hide();

                var clicked = {};

				$(".answer-choice").click(function(){
                    
					let answer_var = $(this).text();
					let question_var = $(this).closest(".survey-item").find(".question").text();
                    let question_num = parseInt($(this).closest(".survey-item").find(".question").attr("id"));
                    let survey_item = $(this).closest(".survey-item");
                    let possible_answers = ["Strongly Disagree", "Disagree", "Neutral", "Agree", "Strongly Agree"];

                    // If has not already been updated
                    if ( question_var in clicked ) { return; }

                    clicked[question_var] = true;
                    $(this).addClass("selected");
                    $('#answers-form').append(`<input type="hidden" name="${question_var}" value="${answer_var}" />`);

                    // Send AJAX Request
                    let request = $.ajax({ type: "POST",   
                            url: "/get-data.php",
                            data: {question:question_var, answer:answer_var},
                    });


                    request.done(function(response, textStatus, jqXHR) {
                        let response_obj = JSON.parse(response);
                        let data = {
                            "Strongly Disagree": parseInt(response_obj[question_num]["strongly_disagree_count"]),
                            "Disagree": parseInt(response_obj[question_num]["disagree_count"]),
                            "Neutral": parseInt(response_obj[question_num]["neutral_count"]),
                            "Agree": parseInt(response_obj[question_num]["agree_count"]),
                            "Strongly Agree": parseInt(response_obj[question_num]["strongly_agree_count"]) 
                        };
                        let response_count = sum(data);
                        let answers = survey_item.find(".answer-container");
                        
                        answers.find(".answer-choice").each(function() {
                            let key = $(this).text()
                            let percentage = (data[key] * 100 / response_count).toFixed(1);
                            $(this).text(`${percentage}%`);
                            if ($(this).hasClass("selected")) {
                                $(this).css({"background":`linear-gradient(0deg, #4F7FA9 ${percentage}%, white ${percentage}%)`});
                            } else {
                                $(this).css({"background":`linear-gradient(0deg, #96D294 ${percentage}%, white ${percentage}%)`});
                            }
                        });
                    });

                    request.fail(function (jqXHR, textStatus, errorThrown){
                        // Log the error to the console
                        console.error(
                            "The following error occurred: "+
                            textStatus, errorThrown
                        );
                    });
				});

                $('form').on('submit', function(e){
                    e.preventDefault();
                });

                let answer_key = {
                    o_positive: ["I have a rich vocabulary.", "I have a vivid imagination.", "I have excellent ideas.", "I am quick to understand things.", "I use difficult words.", "I spend time reflecting on things.", "I am full of ideas."],
                    o_negative: ["I have difficulty understanding abstract ideas.", "I am not interested in abstract ideas.", "I do not have a good imagination."],
                    c_positive: ["I am always prepared.", "I pay attention to details.", "I get chores done right away.", "I like order.", "I follow a schedule.", "I am exacting in my work."],
                    c_negative: ["I leave my belongings around.", "I make a mess of things.", "I often forget to put things back in their proper place.", "I shirk my duties."], 
                    e_positive: ["I am the life of the party.", "I feel comfortable around people.", "I start conversations.", "I talk to a lot of different people at parties.", "I don't mind being the center of attention."],
                    e_negative: ["I don't talk a lot.", "I keep in the background.", "I have little to say.", "I don't like to draw attention to myself.", "I am quiet around strangers."], 
                    a_positive: ["I am interested in people.", "I sympathize with others' feelings.", "I have a soft heart.", "I take time out for others.", "I feel others' emotions.", "I make people feel at ease."],
                    a_negative: ["I feel little concern for others.", "I insult people.", "I am not interested in other people's problems.", "I am not really interested in others."], 
                    n_positive: ["I get stressed out easily.", "I worry about things.", "I am easily disturbed.", "I get upset easily.", "I change my mood a lot.", "I have frequent mood swings.", "I get irritated easily.", "I often feel blue."],
                    n_negative: ["I am relaxed most of the time.", "I seldom feel blue."], 
                }

                $('#submit-answers').click(function() {
                    var o = 0;
                    var c = 0;
                    var e = 0;
                    var a = 0;
                    var n = 0;
                

                    let answer_values = {
                        "Strongly Disagree": -2,
                        "Disagree": -1,
                        "Neutral": 0,
                        "Agree": 1,
                        "Strongly Agree": 2
                    };

                    $('.survey-item').each(function() {
                        let question = $(this).find(".question").text();
                        var answer = -999;
                        if ($(this).find(".answer-container").find(".answer-choice.selected").hasClass("strongly-disagree")) {
                            answer = -2;
                        } else if ($(this).find(".answer-container").find(".answer-choice.selected").hasClass("disagree")) {
                            answer = -1;
                        } else if ($(this).find(".answer-container").find(".answer-choice.selected").hasClass("neutral")) {
                            answer = 0;
                        } else if ($(this).find(".answer-container").find(".answer-choice.selected").hasClass("agree")) {
                            answer = 1;
                        } else if ($(this).find(".answer-container").find(".answer-choice.selected").hasClass("strongly-agree")) {
                            answer = 2;
                        } 

            
                        console.log(`"${question}"`);
                        
                        if (answer != -999) {
                            if (answer_key.o_positive.includes(question)) {
                                
                                o = o + answer;
                            } else if (answer_key.o_negative.includes(question)) {
                                
                                o = o - answer;
                            } else if (answer_key.c_positive.includes(question)) {
                                
                                c = c + answer;
                            } else if (answer_key.c_negative.includes(question)) {
                                
                                c = c - answer;
                            } else if (answer_key.e_positive.includes(question)) {
                                
                                e = e + answer;
                            } else if (answer_key.e_negative.includes(question)) {
                                
                                e = e - answer;
                            } else if (answer_key.a_positive.includes(question)) {
                                
                                a = a + answer;
                            } else if (answer_key.a_negative.includes(question)) {
                                
                                a = a - answer;
                            } else if (answer_key.n_positive.includes(question)) {
                                
                                n = n + answer;
                            } else if (answer_key.n_negative.includes(question)) {
                                
                                n = n - answer;
                            }
                            // console.log(scores);
                        }
                    });
                    console.log([o, c, e, a, n]);

                    $("#openness-results").text(`${o*5}\%`);
                    $("#conscientiousness-results").text(`${c*5}\%`);
                    $("#extroversion-results").text(`${e*5}\%`);
                    $("#agreeableness-results").text(`${a*5}\%`);
                    $("#neuroticism-results").text(`${n*5}\%`);

                    $("#test-results").show();
                    document.getElementById("test-results").scrollIntoView();

                    if (Object.keys(clicked).length < 50) {
                        $("#incomplete-warning").show();
                    }
                });

			});
		</script>
	</head>

    <body>
        <div class="title">
			<h1 class="display-3"> Big Five Personality Comparison </h1>
		</div>
        <div class="content">
        </div>

		<div class="content">

            <?php if ($result && $prepared_stmt->rowCount() > 0) { 
                $i = 0;
                foreach ($result as $row) { ?>
                <div class="survey-item">
                    <div class="question-container row">
                        <h3 class="display-9 question" id="<?php echo $i++; ?>"><?php echo $question_text[$row['COLUMN_NAME']];?></h3>
                    </div>
                    <div class="answer-container row">
                        <div class="col answer-choice strongly-disagree">Strongly Disagree</div>
                        <div class="col answer-choice disagree">Disagree</div>
                        <div class="col answer-choice neutral">Neutral</div>
                        <div class="col answer-choice agree">Agree</div>
                        <div class="col answer-choice strongly-agree">Strongly Agree</div>
                    </div>
                </div>
                <hr>
            <?php }
            } ?>
        <form id="answers-form">
            <button id="submit-answers" type="submit" class="btn btn-dark">Get Results</button>
        </form>
        <div id="test-results">
            <h3 class="display-5">Results</h3>
            <p>Openness: <span id="openness-results"></span></p>
            <p>Conscientiousness: <span id="conscientiousness-results"></p>
            <p>Extroversion: <span id="extroversion-results"></span></p>
            <p>Agreeableness: <span id="agreeableness-results"></span></p>
            <p>Neuroticism: <span id="neuroticism-results"></span></p>
            <span id="incomplete-warning"><p>Note: If you did not answer all of the questions, results may be inaccurate.</p></span>
            <hr>
            <p>What do my results mean?</p>
            <img src="https://www.simplypsychology.org/big-5-personality.jpg?ezimgfmt=rs:553x583/rscb30/ng:webp/ngcb30">
            <p><a href="https://www.verywellmind.com/the-big-five-personality-dimensions-2795422">Click here to learn more.</a></p>
        </div>
	</body>
</html>