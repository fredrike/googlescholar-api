GoogleScholar API
-----------------

Simple API that parses information from https://scholar.google.se/citations. 

Outputs the publications on the first page together with the Citation indeces. Live sample can be found here: http://cse.bth.se/~fer/googlescholar-api/googlescholar.php?user=vJjq9LwAAAAJ do note that the `user=<google-scholar-id>` must be set. Do note that there is no verification of the input variable `user`, this makes it possible to append `%26view_op=list_works%26sortby=pubdate` after the `scholar-id` to get the publications sorted by year (newest first).

Sample output:

```json
{
 "total_citations": 58,
 "citations_per_year": {
  "2012 ": 1 ,
  "2013 ": 7 ,
  "2014 ": 13 ,
  "2015 ": 10 ,
  "2016 ": 23 ,
  "2017 ": 2 
 },
 "publications": [
  {
    "title": "Privacy threats related to user profiling in online social networks",
    "authors": "F Erlandsson, M Boldt, H Johnson",
    "venue": "Privacy, Security, Risk and Trust (PASSAT), 2012 International Conference on ..., 2012 ",
    "citations": 18,
    "year": 2012 
  },
  {
    "title": "SIN: A Platform to Make Interactions in Social Networks Accessible",
    "authors": "SFW Roozbeh Nia, Fredrik Erlandsson, Prantik",
    "venue": "ASE International Conference on Social Informatics, 2012 ",
    "citations": 10,
    "year": 2012
  }
 ]
}
```
  
