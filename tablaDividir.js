const mostrarTabla = (event) => {
	event.preventDefault();
	const numero = Number(document.getElementById('numero').value);

	if (numero >= 0 && numero <= 10) {
		const tabla = document.getElementById('tabla');
		let tablaDivision = `<h2>Tabla de dividir del número ${numero}</h2>`;
		tablaDivision += '<ul>';

		for (let i = 1; i <= 10; i++) {
			tablaDivision += `<li>${numero * i} / ${i} = ${numero}</li>`;
		}

		tablaDivision += '</ul>';
		tabla.innerHTML = tablaDivision;
	} else {
		alert('El número introducido debe estar entre 0 y 10 (ambos inclusive).');
		document.getElementById('numero').value = '';
	}
};
