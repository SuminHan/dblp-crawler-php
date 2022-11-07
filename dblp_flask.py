from io import StringIO
# import urllib.parse
import json
import sys
import pandas as pd
# import geopandas as gpd
import os
import numpy as np
import datetime
from flask import Flask, render_template, request, send_file, json
from flask_cors import CORS, cross_origin
import json
from pymongo import MongoClient 

app = Flask(__name__)
CORS(app, support_credentials=True)


# Making Connection
myclient = MongoClient("mongodb://localhost:27017/") 
db = myclient["dblp_crawled"]
collection = db["papers"]


@app.route("/papers", methods = ['POST', 'GET'])
@cross_origin()
def papers():
    
    if request.method == 'GET':
        cursor = collection.find({}, {'venue':1, 'year':1, 'title':1, 'ee':1, '_id':0})
        data = list(cursor)
        
    if request.method == 'POST':
        print(request.get_data())
        params = json.loads(request.get_data())#, encoding='utf-8')
        print(params)
        cursor = collection.find({'venue':{'$in':params['venue']},
                                  'year': {'$gte': int(params['from_year']), '$lte': int(params['until_year'])}},
                                 {'venue':1, 'year':1, 'title':1, 'ee':1, '_id':0})
        data = list(cursor)
        print(len(data))
    
    response = app.response_class(
        response=json.dumps(data),
        status=200,
        mimetype='application/json'
    )
    return response


if __name__ == "__main__":
    app.run(host="0.0.0.0", port=15111)


