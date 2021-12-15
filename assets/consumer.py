import os  
import time 
from kafka import KafkaConsumer 
import json
import mysql.connector

consumer = KafkaConsumer (bootstrap_servers=["129.114.25.202:9092", "129.114.27.202:9092"]) 

consumer.subscribe (topics=["responses-new"])

db = mysql.connector.connect(
  host="129.114.25.202",
  user="admin",
  password="admin",
  database="big_five_db"
)

columns = ['EXT1', 'EXT2', 'EXT3', 'EXT4', 'EXT5', 'EXT6', 'EXT7', 'EXT8', 'EXT9', 'EXT10', 'EST1', 'EST2', 'EST3', 'EST4', 'EST5', 'EST6', 'EST7', 'EST8', 'EST9', 'EST10', 'AGR1', 'AGR2', 'AGR3', 'AGR4', 'AGR5', 'AGR6', 'AGR7', 'AGR8', 'AGR9', 'AGR10', 'CSN1', 'CSN2', 'CSN3', 'CSN4', 'CSN5', 'CSN6', 'CSN7', 'CSN8', 'CSN9', 'CSN10', 'OPN1', 'OPN2', 'OPN3', 'OPN4', 'OPN5', 'OPN6', 'OPN7', 'OPN8', 'OPN9', 'OPN10']
separator = ' INT, ' 


# CREATE TABLE
cursor = db.cursor()
query = "CREATE TABLE IF NOT EXISTS big_five_db.responses (" + separator.join(columns) + ' INT' + ")"
cursor.execute(query)

# INSERT EACH QUESTION AS ROW 
for msg in consumer:
    json_data = json.loads (msg.value)

    insert_query = ("INSERT INTO big_five_db.responses VALUES (" + ("%s, " * 49) +  "%s)")
    cursor.execute(insert_query, tuple(json_data.values())
)
    db.commit()

cursor.close()
db.close()
consumer.close ()