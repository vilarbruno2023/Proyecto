const tipoEncuesta = document.getElementById("tipoEncuesta");
const segmento = document.getElementById("segmento");
const mensaje = document.getElementById("mensajeEncuesta");
const segmentos = {
  trasplantado: [["trasplante", "Atención de trasplante"], ["seguimiento", "Seguimiento del trasplante"]],
  estandar: [["salud_reproductiva", "Salud Reproductiva"], ["cardiologia", "Cardiología"], ["imagenologia", "Imagenología"], ["nefrologia", "Nefrología"], ["prevencion", "Prevención"], ["otro", "Otro servicio"]],
  fnr: [["fnr_trasplante", "Programa de trasplantes"], ["fnr_cardiologia", "Programa de cardiología"], ["fnr_nefrologia", "Programa de nefrología"], ["fnr_otro", "Otro programa del FNR"]]
};
tipoEncuesta.addEventListener("change", () => {
  segmento.replaceChildren(new Option("Seleccioná", ""));
  for (const [valor, texto] of (segmentos[tipoEncuesta.value] || [])) segmento.add(new Option(texto, valor));
});
const params = new URLSearchParams(location.search);
if (params.get("ok") === "1" || params.get("error") === "1") { mensaje.hidden = false; mensaje.textContent = params.get("ok") === "1" ? "Gracias. Tu respuesta anónima fue registrada." : "No se pudo guardar la respuesta. Revisá los datos e intentá de nuevo."; }
