# Mi Portafolio

Sitio personal de Carlos Marcelo Hernandez, publicado como sitio estático.

## Tecnologías

- **HTML5**: estructura y contenido de las páginas (`index.html`, `sobre-mi.html`, `mis-cursos.html` y `contacto.html`).
- **CSS3**: diseño, temas claro/oscuro, adaptación a pantallas y animaciones (`style.css`).
- **JavaScript (ES6)**: menú móvil, cambio de tema, galerías e interacciones (`script.js`).
- **PHP**: `contacto.php` contiene una alternativa de backend para servidores que ejecuten PHP. GitHub Pages no ejecuta PHP.
- **Google Fonts**: tipografías Roboto y Roboto Slab cargadas desde el servicio de Google Fonts.

No utiliza un framework de frontend ni requiere un proceso de compilación: el navegador carga directamente HTML, CSS y JavaScript.

## Formulario de contacto

El formulario de `contacto.html` envía los datos mediante un POST HTML estándar a [FormSubmit](https://formsubmit.co/), que los reenvía a `cmarcelohernandez@gmail.com`. Esto permite usar el formulario en GitHub Pages sin un servidor PHP propio y evita depender de CORS en el navegador. Los datos del formulario pasan por ese servicio externo.

La primera vez hay que enviar el formulario publicado y confirmar la dirección desde el correo de activación que envía FormSubmit. Hasta completar esa confirmación, los envíos no llegarán normalmente al buzón. Para probar el envío real, el sitio debe estar publicado o servido desde un entorno web; abrir el HTML directamente como archivo no es una prueba fiable.

## Archivos principales

- `index.html`: página de inicio.
- `sobre-mi.html`, `mis-cursos.html`, `contacto.html`: páginas del sitio.
- `style.css`: estilos compartidos.
- `script.js`: interacciones del navegador.
- `contacto.php`: backend PHP alternativo, no utilizado por el formulario actual de GitHub Pages.
- `img/`: imágenes del sitio.

## Publicación

El sitio puede alojarse en GitHub Pages u otro hosting estático. Para utilizar `contacto.php` en lugar de FormSubmit, se necesita un hosting con PHP y correo saliente configurado; GitHub Pages no ofrece esas funciones.
