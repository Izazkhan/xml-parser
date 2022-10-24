Pull/clone this repo:
Note: you should have docker installed on your machine


    git clone https://github.com/Izazkhan/xml-parser.git xmlparser && cd xmlparser && composer install && clear && php xmlparser app:build && cd builds && docker build -t parserapp . && docker run parserapp xml:save --path="storage/coffee-feed-with-dtd.xml" --validate --eh --save --medium=spreadsheet --cfn=storage/google/creds.json --sid='11JFLFFnm_vO2xnTxoXYjJppuolfrL91sU3B-9YDyMg4' --sn="Sheet1"


Everything will just work out :)

Check Google spreadsheet for result:
https://docs.google.com/spreadsheets/d/11JFLFFnm_vO2xnTxoXYjJppuolfrL91sU3B-9YDyMg4/edit?usp=sharing


Play more with command:
- If you just want to validate the file:
```
docker run parserapp xml:save --path="storage/coffee-feed-valid.xml" --validate
```

- If you don't provide path option, the command should print a message accordingly:

 ```
 docker run parserapp xml:save
 ```
 
 Options meaning
 ```
 --eh : Extract headers
 --save: Saving the data to provided medium (spreaddheet)
 --cfn: credentials file path/name
 --sid: SpreadsheetID
 --sn: SheetName
 --validate: to validate file according to provided .dft file
 ```
