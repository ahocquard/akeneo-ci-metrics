SELECT
  row_id as "row_number",
  test_name as "test",
  duration,
  cumulated_duration,
  cumulated_duration/total_duration as "%_cumulated_duration",
  row_id/total_number_tests as "%_cumulated_number_tests"
FROM (
  SELECT
    @curRow := @curRow + 1 as row_id,
    test_name,
    duration,
    SUM(duration) OVER (ORDER BY duration DESC) as cumulated_duration,
    SUM(duration) OVER () as total_duration,
    count(test_name) over() as total_number_tests
  FROM
    test_metric
    JOIN (SELECT @curRow := 0) r
  WHERE
    pipeline_name="$pipeline_name"
    AND branch_name = "$branch_name"
    AND run_id = "$run_id"
    AND type IN ($type)
  ORDER BY duration DESC
) metric;



SELECT SUM(duration)
FROM test_metric
WHERE
  pipeline_name = "$pipeline_name"
  AND branch_name = "$branch_name"
  AND run_id = "$run_id"
  AND type IN ($type);




SELECT AVG(duration)
FROM test_metric
WHERE
  pipeline_name = "$pipeline_name"
  AND branch_name = "$branch_name"
  AND run_id = "$run_id"
  AND type IN ($type);

SELECT COUNT(*)
FROM test_metric
WHERE
  pipeline_name = "$pipeline_name"
  AND branch_name = "$branch_name"
  AND run_id = "$run_id"
  AND type IN ($type);


SELECT execution_time
FROM test_metric
WHERE
  pipeline_name = "$pipeline_name"
  AND branch_name = "$branch_name"
  AND run_id = "$run_id"
LIMIT 1;
