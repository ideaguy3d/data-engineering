import cassandra

from cassandra.cluster import Cluster
try: 
    cluster = Cluster(['127.0.0.1']) #If you have a locally installed Apache Cassandra instance
    session = cluster.connect()
except Exception as e:
    print(e)



try:
    session.execute("""
    CREATE KEYSPACE IF NOT EXISTS php_compute 
    WITH REPLICATION = 
    { 'class' : 'SimpleStrategy', 'replication_factor' : 1 }"""
)

except Exception as e:
    print(e)



try:
    session.set_keyspace('php_compute')
except Exception as e:
    print(e)


query = "CREATE TABLE IF NOT EXISTS music_php "
query = query + "(song_title text, artist_name text, year int, album_name text, single int, PRIMARY KEY (year, artist_name))"
try:
    session.execute(query)
except Exception as e:
    print(e)



query = "insert into music_php (song_title, artist_name, year, single, album_name)" 
query = query + " VALUES (%s, %s, %s, %s, %s)"

try:
    session.execute(query, ("Across The Universe", "The Beatles", 1970, 0, "Let It Be"))
except Exception as e:
    print(e)
    
try:
    session.execute(query, ("Think For Yourself", "The Beatles", 1965, 0, "Rubber Soul"))
except Exception as e:
    print(e)



query = 'SELECT * FROM music_php'
try:
    rows = session.execute(query)
except Exception as e:
    print(e)
    
for row in rows:
    print (row.year, row.album_name, row.artist_name)



query = 'select * from songs WHERE YEAR=1970 AND artist_name="The Beatles"'
try:
    rows = session.execute(query)
except Exception as e:
    print(e)
    
for row in rows:
    print (row.year, row.album_name, row.artist_name)


session.shutdown()
cluster.shutdown()



















#