select l.id, l.nombre, tj.nombre as tipo_juego, p.nombre as plataforma, h.nombre as Hardware from juego j 
	inner join lista l 
	inner join tipo_juego tj 
	inner join plataforma p
	inner join hardware h
	on j.id=l.id and 
	l.tipo_medio='juego' and
	j.tipo_juego=tj.id and
	j.plataforma=p.id;