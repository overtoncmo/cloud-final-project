
#############################################################################
#######   Create table with cached results to improve performance    ########
#############################################################################

import os   
import time 
import mysql.connector


db = mysql.connector.connect(
  host="129.114.25.202",
  user="admin",
  password="admin",
  database="big_five_db"
)



columns = ['EXT1', 'EXT2', 'EXT3', 'EXT4', 'EXT5', 'EXT6', 'EXT7', 'EXT8', 'EXT9', 'EXT10', 'EST1', 'EST2', 'EST3', 'EST4', 'EST5', 'EST6', 'EST7', 'EST8', 'EST9', 'EST10', 'AGR1', 'AGR2', 'AGR3', 'AGR4', 'AGR5', 'AGR6', 'AGR7', 'AGR8', 'AGR9', 'AGR10', 'CSN1', 'CSN2', 'CSN3', 'CSN4', 'CSN5', 'CSN6', 'CSN7', 'CSN8', 'CSN9', 'CSN10', 'OPN1', 'OPN2', 'OPN3', 'OPN4', 'OPN5', 'OPN6', 'OPN7', 'OPN8', 'OPN9', 'OPN10']

cursor = db.cursor()
delete_cache_query = "DROP TABLE IF EXISTS big_five_db.responses_cache"
cursor.execute(delete_cache_query)
create_cache_query = "CREATE TABLE IF NOT EXISTS big_five_db.responses_cache (question_code VARCHAR(10), strongly_disagree_count INT, disagree_count INT, neutral_count INT, agree_count INT, strongly_agree_count INT)"
cursor.execute(create_cache_query)

for col in columns:
    cur_data_query = "SELECT DISTINCT " + col + ", COUNT(*) FROM big_five_db.responses GROUP BY " + col + " HAVING " + col + " BETWEEN 1 AND 5 ORDER BY " + col + ";"
    cursor.execute(cur_data_query)
    result = cursor.fetchall()
    insert_query = ("INSERT INTO big_five_db.responses_cache VALUES (%s, %s, %s, %s, %s, %s)")
    data = tuple([col] + [num for _, num in result])

    cursor.execute(insert_query, data)
    db.commit()

cursor.close()
db.close()