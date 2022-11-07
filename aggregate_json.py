import os, json
import pandas as pd
from pymongo import MongoClient 

all_papers = []
for fname in sorted(os.listdir('json_data')):
    if fname[-4:] == 'json':
        with open('json_data/' + fname) as fp:
            json_data = json.load(fp)
        print(fname, len(json_data['result']['hits']['hit']))
        
        all_papers.extend(json_data['result']['hits']['hit'])

df = pd.DataFrame([d['info'] for d in all_papers])
df = df[df['venue'] == df['venue']]
df = df[['title', 'venue', 'year', 'ee']]
df['year'] = df['year'].astype(int)
venues = []
for venue in df['venue']:
    if type(venue) == list:
        venues.append(venue[0])
    else:
        venues.append(venue)
df['venue'] = venues

df.to_json('summary_papers.json', orient='records')

print('SUCCESS (1/3): save summary_papers.json')

d = dict(df.value_counts(['venue', 'year']))
md_list = []
for k in d:
    md = dict()
    md['venue'] = k[0]
    md['year'] = k[1]
    md['count'] = int(d[k])
    md_list.append(md)
    
with open('conf_year_value_counts.json', 'w') as fp:
    json.dump(md_list, fp)

print('SUCCESS (2/3): save conf_year_value_counts.json')

  
# Making Connection
myclient = MongoClient("mongodb://localhost:27017/") 
   
# database 
db = myclient["dblp_crawled"]
   
# Created or Switched to collection 
# names: GeeksForGeeks
collection = db["papers"]
  
# Loading or Opening the json file
with open('summary_papers.json') as file:
    file_data = json.load(file)

collection.drop()
collection.insert_many(file_data)

print('SUCCESS (3/3): insert mongodb')
