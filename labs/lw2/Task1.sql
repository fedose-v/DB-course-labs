-- 1
EXPLAIN ANALYZE
SELECT *
FROM flights
WHERE flight_no LIKE '%488'
; 

-- -> Filter: (flights.flight_no like '%488')  (cost=6650 rows=7290) (actual time=52.7..204 rows=121 loops=1)
--      -> Table scan on flights  (cost=6650 rows=65619) (actual time=2.94..177 rows=65664 loops=1)
 

-- 2 
EXPLAIN ANALYZE
SELECT * 
FROM flights
WHERE departure_airport = 'KRR'
  OR arrival_airport = 'KRR'
;

-- -> Filter: ((flights.departure_airport = 'KRR') or (flights.arrival_airport = 'KRR'))  (cost=408 rows=932) (actual time=0.159..4.2 rows=935 loops=1)
--      -> Deduplicate rows sorted by row ID  (cost=408 rows=932) (actual time=0.155..3.83 rows=935 loops=1)
--          -> Index range scan on flights using flights_departure_airport_fkey over (departure_airport = 'KRR')  (cost=48.6 rows=468) (actual time=0.102..0.565 rows=468 loops=1)
--          -> Index range scan on flights using flights_arrival_airport_fkey over (arrival_airport = 'KRR')  (cost=48.6 rows=467) (actual time=0.0307..0.515 rows=467 loops=1)
--  

-- 3
-- Index Merge
EXPLAIN ANALYZE
SELECT * 
FROM flights
WHERE (departure_airport = 'CSY' AND aircraft_code = 'SU9')
  OR (arrival_airport = 'CSY' AND aircraft_code = 'SU9')
;

-- -> Filter: (((flights.aircraft_code = 'SU9') and (flights.departure_airport = 'CSY')) or ((flights.aircraft_code = 'SU9') and (flights.arrival_airport = 'CSY')))  
-- 	(cost=325 rows=600) (actual time=38.6..151 rows=484 loops=1)
--      -> Index lookup on flights using flights_aircraft_code_fkey (aircraft_code='SU9'), with index condition: 
--      ((flights.aircraft_code = 'SU9') or (flights.aircraft_code = 'SU9'))  (cost=325 rows=30394) (actual time=2.24..134 rows=16870 loops=1)

EXPLAIN ANALYZE
SELECT * 
FROM flights
WHERE (departure_airport = 'CSY' OR arrival_airport = 'CSY')
  AND aircraft_code = 'SU9'
;

-- -> Filter: ((flights.aircraft_code = 'SU9') and ((flights.departure_airport = 'CSY') or (flights.arrival_airport = 'CSY')))  (cost=509 rows=558) (actual time=0.544..4.79 rows=484 loops=1)
--      -> Deduplicate rows sorted by row ID  (cost=509 rows=1205) (actual time=0.13..4.32 rows=1210 loops=1)
--          -> Index range scan on flights using flights_departure_airport_fkey over (departure_airport = 'CSY')  (cost=62.6 rows=605) (actual time=0.0809..0.541 rows=605 loops=1)
--          -> Index range scan on flights using flights_arrival_airport_fkey over (arrival_airport = 'CSY')  (cost=62.6 rows=605) (actual time=0.0299..0.488 rows=605 loops=1)


-- 4
EXPLAIN ANALYZE
SELECT 
	b.book_ref,
	b.total_amount
FROM bookings b
ORDER BY total_amount DESC
LIMIT 10
;

-- -> Limit: 10 row(s)  (cost=59628 rows=10) (actual time=1127..1127 rows=10 loops=1)
--      -> Sort: b.total_amount DESC, limit input to 10 row(s) per chunk  (cost=59628 rows=592676) (actual time=1127..1127 rows=10 loops=1)
--          -> Table scan on b  (cost=59628 rows=592676) (actual time=2.31..807 rows=593433 loops=1)
 

-- 5
EXPLAIN ANALYZE
SELECT 
	t.passenger_name,
    t.contact_data	
FROM tickets t
	INNER JOIN bookings b ON b.book_ref = t.book_ref
ORDER BY b.total_amount DESC
LIMIT 1
;
-- -> Limit: 1 row(s)  (cost=749697 rows=1) (actual time=1172..1172 rows=1 loops=1)
--      -> Nested loop inner join  (cost=749697 rows=838153) (actual time=1172..1172 rows=1 loops=1)
--          -> Sort: b.total_amount DESC  (cost=59628 rows=592676) (actual time=1168..1168 rows=1 loops=1)
--              -> Table scan on b  (cost=59628 rows=592676) (actual time=0.0608..440 rows=593433 loops=1)
--          -> Index lookup on t using tickets_book_ref_fkey (book_ref=b.book_ref)  (cost=1.02 rows=1.41) (actual time=3.41..3.41 rows=1 loops=1)
 

-- 6
-- Упростить запрос
EXPLAIN ANALYZE 
SELECT DISTINCT
	ad.aircraft_code
FROM aircrafts_data ad
	INNER JOIN seats s ON ad.aircraft_code = s.aircraft_code
WHERE s.fare_conditions = 'Comfort'
;

-- -> Table scan on <temporary>  (cost=13.8..16.2 rows=9) (actual time=4.78..4.78 rows=1 loops=1)
--      -> Temporary table with deduplication  (cost=13.5..13.5 rows=9) (actual time=4.78..4.78 rows=1 loops=1)
--          -> Nested loop inner join  (cost=12.6 rows=9) (actual time=3.79..4.73 rows=1 loops=1)
--              -> Covering index scan on ad using PRIMARY  (cost=1.15 rows=9) (actual time=0.772..0.781 rows=9 loops=1)
--              -> Limit: 1 row(s)  (cost=0.443 rows=1) (actual time=0.437..0.437 rows=0.111 loops=9)
--                  -> Filter: (s.fare_conditions = 'Comfort')  (cost=0.443 rows=14.9) (actual time=0.436..0.436 rows=0.111 loops=9)
--                      -> Index lookup on s using PRIMARY (aircraft_code=ad.aircraft_code)  (cost=0.443 rows=149) (actual time=0.258..0.414 rows=104 loops=9)
--  
 
 SELECT * FROM aircrafts_data;