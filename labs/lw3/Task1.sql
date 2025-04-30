-- 1
EXPLAIN ANALYZE
SELECT 
	passenger_name,
    f.flight_no,
    actual_departure,
    actual_arrival
FROM tickets t
	INNER JOIN ticket_flights tf ON t.ticket_no = tf.ticket_no
    INNER JOIN flights f ON tf.flight_id = f.flight_id
WHERE book_ref = '58DF57'
;

-- nested loop (внутр и внеш) для R, S, T записать псевдокод
-- foreach (r in R)
--   if (r->T)
--   foreach (s in S)
--     result = r + s

-- Агрегирование данных — это сбор информации из баз данных с целью подготовки комбинированных наборов данных для обработки данных.
-- Агрегирующие функции получают списки значений и возвращают одно значение

-- -> Nested loop inner join  (cost=15.2 rows=8.51) (actual time=0.0908..0.138 rows=3 loops=1)
--      -> Nested loop inner join  (cost=5.82 rows=8.51) (actual time=0.0741..0.119 rows=3 loops=1)
--          -> Index lookup on t using tickets_book_ref_fkey (book_ref='58DF57'), with index condition: (t.book_ref = '58DF57')  (cost=1.9 rows=3) (actual time=0.0494..0.069 rows=3 loops=1)
--          -> Covering index lookup on tf using PRIMARY (ticket_no=t.ticket_no)  (cost=1.12 rows=2.84) (actual time=0.0131..0.0156 rows=1 loops=3)
--      -> Single-row index lookup on f using PRIMARY (flight_id=tf.flight_id)  (cost=1.01 rows=1) (actual time=0.00537..0.0054 rows=1 loops=3)
 
-- 2
-- EXPLAIN ANALYZE
SELECT
	aircraft_code,
    fare_conditions,
    COUNT(aircraft_code) AS seat_count
FROM seats
GROUP BY aircraft_code, fare_conditions
;
 
-- -> Table scan on <temporary>  (actual time=1.32..1.32 rows=17 loops=1)
--      -> Aggregate using temporary table  (actual time=1.32..1.32 rows=17 loops=1)
--         -> Nested loop inner join  (cost=138 rows=1339) (actual time=0.0702..0.619 rows=1339 loops=1)
--              -> Covering index scan on ad using PRIMARY  (cost=1.15 rows=9) (actual time=0.0211..0.0234 rows=9 loops=1)
--              -> Index lookup on s using PRIMARY (aircraft_code=ad.aircraft_code)  (cost=1.93 rows=149) (actual time=0.0246..0.0602 rows=149 loops=9)
 
 
-- 3
-- EXPLAIN ANALYZE
SELECT
	b.book_ref,
	GROUP_CONCAT(passenger_name) AS passenger_names
FROM tickets t
	INNER JOIN bookings b ON t.book_ref = b.book_ref
WHERE SUBSTRING(t.book_ref, 1, 3) = SUBSTRING(t.book_ref, 4, 3)
GROUP BY b.book_ref
;

-- времени для выполнения фактического запроса (actual time=x..y, где x - получение первой строки, y - получение последней строки)
-- сколько строк ожидал оптимизатор и сколько получил (rows=815293 - ожидал, rows=196 - получил)

-- -> Nested loop inner join  (cost=589654 rows=815293) (actual time=5.22..2264 rows=196 loops=1)
--      -> Filter: (substr(t.book_ref,1,3) = substr(t.book_ref,4,3))  (cost=85794 rows=815293) (actual time=5.19..2226 rows=196 loops=1)
--          -> Table scan on t  (cost=85794 rows=815293) (actual time=2.25..1953 rows=829057 loops=1)
--      -> Single-row index lookup on b using PRIMARY (book_ref=t.book_ref)  (cost=0.518 rows=1) (actual time=0.191..0.191 rows=1 loops=196)


-- 4
EXPLAIN ANALYZE
SELECT 
	flight_no,
    actual_departure,
    actual_arrival
FROM flights
WHERE status = 'Arrived'
	AND departure_airport = 'KRR'
	AND arrival_airport = 'KGD'
ORDER BY actual_departure DESC
LIMIT 1
;

-- -> Limit: 1 row(s)  (cost=5.5 rows=1) (actual time=4.75..4.75 rows=1 loops=1)
--      -> Sort: flights.actual_departure DESC, limit input to 1 row(s) per chunk  (cost=5.5 rows=2.23) (actual time=4.74..4.74 rows=1 loops=1)
--          -> Filter: ((flights.arrival_airport = 'KGD') and (flights.departure_airport = 'KRR') and (flights.`status` = 'Arrived'))  (cost=5.5 rows=2.23) (actual time=3.69..4.31 rows=26 loops=1)
--              -> Intersect rows sorted by row ID  (cost=5.5 rows=2.23) (actual time=3.66..4.24 rows=35 loops=1)
--                  -> Index range scan on flights using flights_arrival_airport_fkey over (arrival_airport = 'KGD')  (cost=1.61 rows=312) (actual time=0.122..0.413 rows=312 loops=1)
--                  -> Index range scan on flights using flights_departure_airport_fkey over (departure_airport = 'KRR')  (cost=1.91 rows=468) (actual time=0.0377..0.382 rows=468 loops=1)
 ;

-- -> Limit: 1 row(s)  (cost=4.1 rows=1) (actual time=0.448..0.448 rows=1 loops=1)
--      -> Sort: flights.actual_departure DESC, limit input to 1 row(s) per chunk  (cost=4.1 rows=2.23) (actual time=0.447..0.447 rows=1 loops=1)
--          -> Filter: ((flights.arrival_airport = 'KGD') and (flights.departure_airport = 'KRR') and (flights.`status` = 'Arrived'))  (cost=4.1 rows=2.23) (actual time=0.345..0.423 rows=26 loops=1)
--              -> Intersect rows sorted by row ID  (cost=4.1 rows=2.23) (actual time=0.343..0.409 rows=35 loops=1)
--                  -> Index range scan on flights using flights_arrival_airport_fkey over (arrival_airport = 'KGD')  (cost=1.55 rows=312) (actual time=0.0496..0.161 rows=312 loops=1)
--                  -> Index range scan on flights using flights_departure_airport_fkey over (departure_airport = 'KRR')  (cost=1.85 rows=468) (actual time=0.0138..0.173 rows=468 loops=1)
 
 -- 5
 -- EXPLAIN ANALYZE
 SELECT 
	f.flight_no,
    actual_departure,
    SUM(tf.amount) AS revenue
 FROM flights f
	INNER JOIN ticket_flights tf ON f.flight_id = tf.flight_id
WHERE f.status = 'Arrived'
GROUP BY tf.flight_id
ORDER BY revenue DESC
LIMIT 10
;


--  использовать подзапрос (complete)
-- 6
-- EXPLAIN ANALYZE
SELECT 
	f.flight_no, 
	f.scheduled_departure,
	economy_seats - COUNT(tf.ticket_no) AS empty_seats
FROM (
		SELECT 
			aircraft_code,
			COUNT(seat_no) AS economy_seats
        FROM seats
        WHERE fare_conditions = 'Economy'
        GROUP BY aircraft_code
    ) AS s
	INNER JOIN flights f ON f.aircraft_code = s.aircraft_code
	INNER JOIN ticket_flights tf ON f.flight_id = tf.flight_id  
WHERE f.status = 'Scheduled' 
	AND departure_airport = 'VVO'
	AND arrival_airport IN ('DME', 'SVO', 'VKO')
	AND tf.fare_conditions = 'Economy'
GROUP BY f.flight_id
ORDER BY f.scheduled_departure DESC
LIMIT 1
;

-- -> Limit: 1 row(s)  (actual time=8.79..8.79 rows=1 loops=1)
--      -> Sort: f.scheduled_departure DESC, limit input to 1 row(s) per chunk  (actual time=8.79..8.79 rows=1 loops=1)
--          -> Stream results  (cost=729 rows=332) (actual time=2.22..8.76 rows=28 loops=1)
--              -> Group aggregate: count(distinct tf.ticket_no)  (cost=729 rows=332) (actual time=2.22..8.73 rows=28 loops=1)
--                 -> Nested loop inner join  (cost=696 rows=332) (actual time=1.78..8.07 rows=608 loops=1)
--                      -> Nested loop inner join  (cost=568 rows=36.9) (actual time=0.584..6.27 rows=608 loops=1)
--                          -> Filter: ((f.`status` = 'Scheduled') and (f.arrival_airport in ('DME','SVO','VKO')))  (cost=325 rows=8.68) (actual time=0.256..1.04 rows=28 loops=1)
--                              -> Index lookup on f using flights_departure_airport_fkey (departure_airport='VVO'), with index condition: (f.departure_airport = 'VVO')  (cost=325 rows=363) (actual time=0.251..0.946 rows=363 loops=1)
--                          -> Filter: (tf.fare_conditions = 'Economy')  (cost=23.8 rows=4.25) (actual time=0.172..0.185 rows=21.7 loops=28)
--                              -> Index lookup on tf using ticket_flights_flight_id_fkey (flight_id=f.flight_id)  (cost=23.8 rows=42.5) (actual time=0.172..0.179 rows=24.9 loops=28)
--                      -> Index lookup on s using <auto_key0> (aircraft_code=f.aircraft_code)  (cost=150..153 rows=10.2) (actual time=0.00257..0.00277 rows=1 loops=608)
--                          -> Materialize  (cost=150..150 rows=9) (actual time=1.18..1.18 rows=9 loops=1)
--                              -> Group aggregate: count(seats.seat_no)  (cost=149 rows=9) (actual time=0.101..1.14 rows=9 loops=1)
--                                 -> Filter: (seats.fare_conditions = 'Economy')  (cost=136 rows=134) (actual time=0.0278..0.942 rows=1139 loops=1)
--                                    -> Index scan on seats using PRIMARY  (cost=136 rows=1339) (actual time=0.0269..0.637 rows=1339 loops=1)
 