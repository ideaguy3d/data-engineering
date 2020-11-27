# creating a table in PostgresQL

import psycopg2


try: 
    conn = psycopg2.connect("host=127.0.0.1 dbname=studentdb user=student password=student")
except psycopg2.Error as e: 
    print("Error: Could not make connection to the Postgres database")
    print(e)



try: 
    cur = conn.cursor()
except psycopg2.Error as e: 
    print("Error: Could not get curser to the Database")
    print(e)


# set autocommit to true
conn.set_session(autocommit=True)    


try: 
    cur.execute("create database php_ai")
except psycopg2.Error as e:
    print(e)


try: 
    conn.close()
except psycopg2.Error as e:
    print(e)
    

try: 
    conn = psycopg2.connect("host=127.0.0.1 dbname=php_ai user=student password=student")
except psycopg2.Error as e: 
    print("Error: Could not make connection to the Postgres database")
    print(e)
    

try: 
    cur = conn.cursor()
except psycopg2.Error as e: 
    print("Error: Could not get curser to the Database")
    print(e)


conn.set_session(autocommit=True)


try: 
    cur.execute("CREATE TABLE IF NOT EXISTS php_music_perceptron_1 ("+
                "artist_name varchar(56), year int, album_name varchar(128), single bool);")
except psycopg2.Error as e: 
    print("Error: Issue creating table")
    print (e)


try: 
    #"alter table php_music_perceptron_1 add song_title varchar(128);"+ 
    cur.execute(
                "INSERT INTO php_music_perceptron_1 (song_title, artist_name,  year,single, album_name) \
                 VALUES (%s, %s, %s, %s, %s)", \
                 ("Across The Universe", "The Beatles", "1970", "False", "Let It Be"))
except psycopg2.Error as e: 
    print("Error: Inserting Rows")
    print (e)
    
try: 
    #"alter table php_music_perceptron_1 add song_title varchar(128);"+
    cur.execute(
                "INSERT INTO php_music_perceptron_1 (song_title, artist_name, single, year, album_name) \
                  VALUES (%s, %s, %s, %s, %s)", \
                  ("The Beatles", "Think For Yourself", "False", "1965", "Rubber Soul"))
except psycopg2.Error as e: 
    print("Error: Inserting Rows")
    print (e)


try: 
    cur.execute("SELECT * FROM php_music_perceptron_1;")
except psycopg2.Error as e: 
    print("Error: select *")
    print (e)

row = cur.fetchone()
while row:
   print(row)
   row = cur.fetchone()


cur.close()
conn.close()


#