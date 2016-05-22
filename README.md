# vanhackathon_axiom-zen_mastermind_API

This project refers to a challenge of VanHackathon, started in May, 20.

Challenge: Mastermind Backend Implementation

Are you a backend architect that can spin up robust, well tested APIs? Then this may be the challenge for you.
Build an API that you can play Mastermind with! The language and stack you choose to use is it up to you. The endpoints you create and the structure are up to you but it must be able to support multiple users hitting the API at the same time playing different games of Mastermind and the API must respond with the proper number of exact and near matches for every guess.
To go a bit further, try implementing a multiplayer component to the game where a user can hit an endpoint to create a game and wait for another user to join the same game. Once the second user joins, the API waits for guesses from both users and responds to both users with its response once both guesses come in. The user that guesses the code first wins and the final response shows both users' guessing history.

# Install
1) The aplication was developed in PHP

2) Before running, create a MySQL database using "database.sql" file and change variables "connection_db" in the "config.php" file. 


# Example

1) Start a new game:

POST 
Endpoint: /new_game 
Json: {"user_name":"peter_example"}

Response:
{"status":"success","data":{"code_length":8,"colors":["B","O","C","M","C","G","Y","C"],"game_key":"6364d3f0f495b6ab9dcf8d3b5c6e0b013f6a06251c3f26598c2edb8abc3b8fed","game_users":["peter_example"],"guess":[],"num_guesses":0,"past_results":[],"result":[],"solved":false}}


2) Send a guess:

POST 
Endpoint: /guess
Json: {"game_key":"6364d3f0f495b6ab9dcf8d3b5c6e0b013f6a06251c3f26598c2edb8abc3b8fed","user_name":"peter_example","colors":["O","Y","G","B","G","B","G","Y"]}

Response:
{"status":"success","data":{"code_length":8,"colors":["B","O","C","M","C","G","Y","C"],"game_key":"6364d3f0f495b6ab9dcf8d3b5c6e0b013f6a06251c3f26598c2edb8abc3b8fed","game_users":["peter_example"],"guess":["O","Y","G","B","G","B","G","Y"],"num_guesses":1,"past_results":[{"user_name":"peter_example","colors":["O","Y","G","B","G","B","G","Y"],"exact":"0","near":"4","creation_date":"2016-05-22 18:34:40"}],"result":{"exact":"0","near":"4"},"status":{"solved":false,"solved_by_user":null,"user_started_in_game":null,"solution_date":"0000-00-00 00:00:00","time":""}}}


3) Multiplayer
POST /multiplayer
Json:
{"game_key":"6364d3f0f495b6ab9dcf8d3b5c6e0b013f6a06251c3f26598c2edb8abc3b8fed","user_name":"john_user"}

Response:
{"status":"success","data":{"code_length":8,"colors":["B","O","C","M","C","G","Y","C"],"game_key":"6364d3f0f495b6ab9dcf8d3b5c6e0b013f6a06251c3f26598c2edb8abc3b8fed","game_users":["john_user","peter_example"],"guess":[],"num_guesses":1,"past_results":[{"user_name":"peter_example","colors":["O","Y","G","B","G","B","G","Y"],"exact":"0","near":"4","creation_date":"2016-05-22 18:34:40"}],"result":{"exact":"0","near":"4"},"status":{"solved":false,"solved_by_user":null,"user_started_in_game":null,"solution_date":"0000-00-00 00:00:00","time":""}}}

And after that, use a guess method.
