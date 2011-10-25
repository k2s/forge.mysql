CREATE DEFINER=`root`@`%` FUNCTION `WorkDayDiff`(b date, a date) RETURNS int(11)
DETERMINISTIC
COMMENT 'working day difference for 2 dates'
BEGIN
	DECLARE freedays int;
	SET freedays = 0;

	SET @x = DATEDIFF(b, a);
	IF @x<0 THEN
		SET @m = a;
		SET a = b;
		SET b = @m;
		SET @m = -1;
	ELSE
		SET @m = 1;
	END IF;
	SET @x = abs(@x) + 1;
	/* days in first week */
	SET @w1 = WEEKDAY(a)+1;
	SET @wx1 = 8-@w1;
	IF @w1>5 THEN
		SET @w1 = 0;
	ELSE
		SET @w1 = 6-@w1;
	END IF;
	/* days in last week */
	SET @wx2 = WEEKDAY(b)+1;
	SET @w2 = @wx2;
	IF @w2>5 THEN
		SET @w2 = 5;
	END IF;
	/* summary */
	SET @weeks = (@x-@wx1-@wx2)/7;
	SET @noweekends = (@weeks*5)+@w1+@w2;
	/* Uncomment this if you want exclude also holidays
	SELECT count(*) INTO freedays FROM holiday WHERE d_day BETWEEN a AND b AND WEEKDAY(d_day)<5;
	*/
	SET @result = @noweekends-freedays;
	RETURN @result*@m;
END$$

DELIMITER ;
