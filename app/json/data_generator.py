'''
The dataset is a JSON array. Here's a sample with a single element:

[
  {
    "date": "2014-08-01",
    "pct05": 5350,
    "pct25": 6756,
    "pct50": 7819,
    "pct75": 9284,
    "pct95": 13835
  },
]

the 5th and 95 percentiles determine the light-blue span
the 25th and 75th percentiles determine the dark-blue span
the 50th percentile is the black line

the curve smoothes out automatically
the start and end dates are 2014-08-01 and 2014-09-08. 31 dates in august
the sale period depends on marker.json,
but starts 2014-08-13 and ends 2014-08-28
'''

import json, random
array = []
start, end = 10, 25
for n in range(1, 40):
    month = 8+n//31
    day = n%31
    date = "2014-{:0>2}-{:0>2}".format(month, day)
    entry = {"date": date}
    entry['pct05'] = entry['pct25'] = entry['pct50'] = entry['pct75'] = entry['pct95'] = 0
    if n > end:
        entry['pct75'] = 200 + 5 * n * random.random()
        entry['pct95'] = 280 + 5 * n * random.random()
    elif n > start:
        entry['pct75'] = 400 + 10 * n * random.random()
        entry['pct95'] = 500 + 50 * n**0.7 * random.random()
    else:
        entry['pct75'] = 200 + 5 * n * random.random()
        entry['pct95'] = 280 + 50 * random.random()

    array.append(entry)

json.dump(array, open('data.json', 'w'))
