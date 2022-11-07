import requests
import json
import argparse
import sys


parser = argparse.ArgumentParser()
parser.add_argument('-q', '--query', type=str, help='e.g. db/conf/www/www2016.bht')
args = parser.parse_args()

if len(sys.argv)==1:
    # display help message when no args are passed.
    parser.print_help()
    sys.exit(1)


def crawl_dblp(query):
	conf = query.split('/')[-1]
	conf = conf.split('.')[0]
	fidx = 0
	hits = 1000

	miter = 0
	while True:
		url = f'https://dblp.org/search/publ/api?q=toc%3A{query}%3A&f={fidx}&h={hits}&format=json'
		print(url)

		response = requests.get(url) 
		json_data = response.json()

		if int(json_data['result']['hits']['@total']) == 0:
			print('FAILED')
			break

		with open('json_data/' + conf + f'_{miter}.json', 'w') as fp:
			json.dump(json_data, fp)
		print('SUCCESS with {} results.'.format(len(json_data['result']['hits']['hit'])), flush=True)

		fidx += hits
		if fidx > int(json_data['result']['hits']['@total']):
			break
		miter += 1


if __name__ == "__main__":
	print(args.query)
	crawl_dblp(args.query)


