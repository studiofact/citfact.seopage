DELETE FROM b_citfact_uservars
WHERE group_id IN (
			SELECT b_citfact_uservars_group.ID
			FROM b_citfact_uservars_group
			WHERE b_citfact_uservars_group.CODE = 'seoPage'
			);
	  
	  
DELETE FROM b_citfact_uservars_group
WHERE b_citfact_uservars_group.CODE = 'seoPage';
