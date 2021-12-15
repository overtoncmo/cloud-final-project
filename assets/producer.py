import json
from kafka import KafkaProducer  # producer of events

producer = KafkaProducer (bootstrap_servers="129.114.25.202:9092")

f = open("data.csv")
lines = f.readlines()
f.close()

columns = list(lines[0].split('\t'))
columns = columns[:50]

for line in lines[1:]:
    response = list(line.split('\t'))[:50]

    buf = json.dumps({columns[i]:response[i] for i in range(len(columns))})
    producer.send ("responses-new", value=bytes(buf, 'ascii'))
    producer.flush()

producer.close ()