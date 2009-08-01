<?php require("header.php"); ?>
<div id="banderas">
	<p><img src="images/planetalinux.png" align="right"></p>
</div>
<div id="contenido">
<div id="faq" align="left">
<fieldset id="recuadro2">
<legend>Infraestructura</legend>

<p><b>Planeta Linux</b> es administrado por medio de un repositorio bajo <aa href="http://github.com/damog/planetalinux">GitHub</a>.

<h2>Estructura del árbol</h2>
<p>El árbol tiene cinco directorios en su nivel más alto:</p>
<pre>
planetalinux
|
+-- conf
|
+-- proc
|
+-- sandbox
|
+-- tools
|
+-- www
</pre>
<p><tt>conf</tt> es donde se encuentra la configuración (a nivel servidor) de los diferentes componentes de planetalinux, lease, configuración de apache para el blog, el sitio principal, el RT, etc.
<tt>proc</tt> es donde la mayor parte de los planetas a procesarse vive.
<tt>sandbox</tt> es un directorio donde aquellos que utilizan subversion por primera vez pueden experimentar.
En <tt>tools</tt> pueden ir scripts o aplicaciones que corran con Planeta Linux pero que no sean vitales para su funcionamiento.
<tt>www</tt> son todas las imágenes o HTMLs estáticos o dinámicos que serán desplegados en varias partes de  la página.</p>

<h3><tt>conf</tt></h3>
<p>Dentro de este directorio se tiene básicamente la configuración usada por el servidor WWW apache para los diversos sitios que conforman planetalinux. Al momento se encuentra un solo archivo (apache.conf) con toda la configuración necesaria.
<h3><tt>proc</tt></h3>
<p>Dentro de <tt>proc</tt> vamos a tener los directorios de cada una de las instancias
existentes en Planeta Linux, y además dos archivos genéricos que todas las instancias ocuparán:</p>

<pre>
|
+-- proc
    |
    +-- rss20-new.xml.tmpl
    |
    +-- rss20-universo.xml.tmpl
    |
    +-- mx
    |   |
    |   +-- config.ini
    |   |
    |   +-- index.html.tmpl
    |
    +-- pe
    |   |
    |   +-- config.ini
    |   |
    |   +-- index.html.tmpl
    |
    +-- ve
    |   |
    |   +-- config.ini
    |   |
    |   +-- index.html.tmpl
    |
    + ...
</pre>

<p>Básicamente, <tt>opml.xml.tmpl</tt> y <tt>rss20.xml.tmpl</tt> son los templates de ambos formatos de feeds
y seguramente no tendrás que editarlos a menos que tengas una razón extremadamente válida. Dentro de cada directorio
de instancia, encontrarás <tt>config.ini</tt> e <tt>index.html.tmpl</tt>. El primero será el que la mayoría
de los colaboradores de Planeta Linux tienen que editar, tanto para agregar feeds, editarlos, borrarlos. El
<tt>index.html.tmpl</tt> es el template para la página de cada una de la instancias. Ojalá en algún momento podamos
tener un sólo template para todas las instancias, por el momento no lo tenemos así. También necesitas una suficientemente
buena razón para editar los templates.</p>

<h3><tt>test</tt></h3>
<p>Por el momento contamos con un sólo archivo dentro de <tt>test</tt>:

<pre>
|
+-- test
    |
    +-- hola.pl
</pre>

<p>Este único archivo sirve para que hagas tu primer commit, edítalo y envía el cambio. Si todo sale bien, verás
el diff del cambio que hiciste en la lista de correos. Obviamente no necesitas saber Perl para editarlo, es un
simple archivo de pruebas.</p>

<h3><tt>tools</tt></h3>
<p>En este directorio se pretenden subir cualquier tipo de herramientas que puedan ser de utilidad para Planeta
Linux, aunque por el momento no hay mucho que digamos:</p>

<pre>
|
+-- tools
    |
    +-- icons
        |
        +-- banderas
            |
            +-- ad.png
            |
            +-- ae.png
            |
            +-- af.png
            |
            +-- ...
</pre>

<p>En el momento de escribir este documento, únicamente tenemos los íconos de la banderas que se utilizan
en cada una de las instancias para ligar a las otras. Esas banderitas fueron algo difíciles de encontrar
y loas guardamos ahí para que no se nos pierdan otra vez :-)</pre>

<h3><tt>www</tt></h3>
<p><tt>www</tt> es el directorio más grande donde se almacena la mayor parte de los scripts PHP, código en HTML,
imágenes, cabezas, etc.</p>

<pre>
|
+-- www
    |
    +-- css
    |   |
    |   +-- alternativa.css
    |   |
    |   +-- ...
    |
    +-- images
    |   |
    |   +-- arte
    |   |   |
    |   |   +-- planetalinux-main.png
    |   |   |
    |   |   +-- ...
    |   |
    |   +-- banderas
    |   |   |
    |   |   +-- alternativa.png
    |   |   |
    |   |   +-- ...
    |   |
    |   +-- mar06
    |   |   |
    |   |   +-- ...
    |   |
    |   +-- astrata.png
    |   |
    |   +-- ...
    |
    +-- instancias
    |   |
    |   +-- mx
    |   |   |
    |   |   +-- images
    |   |       |
    |   |       +-- cabezas
    |   |           |
    |   |           +-- accesshigh.png
    |   |           |
    |   |           +-- ahioros.png
    |   |           |
    |   |           +-- ...
    |   |
    |   +-- ve
    |   |   |
    |   |   ...
    |   |
    |   +-- ...
    |
    +-- misc
    |
    +-- principal
    |   |
    |   +-- acerca
</pre>

<h2>Uso de contraseñas</h2>
<p>La autenticación para el repositorio no se efectúa mediante contraseñas. Si estás leyendo esta página es
por que muy probablemente ya tengas una cuenta de acceso y tu llave pública SSH ya esté en el servidor, si no asegúrate
de que <a href="http://damog.net/">damog</a> te agregue.</p>

<p>Esa es la única posibilidad para acceder al repositorio, por medio de llaves SSH en GitHub.</p>

<h2>Obtener el árbol en una copia local</h2>

<p>Para clonar todo el contenido del árbol de Planeta Linux a tu máquina, bastará con ejecutar:</p>

<p><tt>$ git clone git@github.com:damog/planetalinux.git</tt></p>

<p>Es muy importante que tu
llave local SSH sea la que estés usando desde <tt>~/.ssh/id_rsa.pub</tt>, de otra forma no será posible autenticarse
tu cuenta.</p>

<h2>Copia local siempre actualizada</h2>
<p>Es vital, antes de hacer alguna modificación en el árbol, que mantengas tu árbol sincronizado, ejecutando:</p>

<p><tt>git pull origin master</tt></p>

<h2>Agregar feeds o modificar los ya existentes</h2>
<p>Cualquier modificación de nombres debe hacerse en UTF-8, pues así es como Planet entiende los acentos y tildes. 
Asegúrate de que tu cliente esté leyendo y escribiendo en UTF-8.</p>

<p>Para agregar feeds, basta con utilizar tu editor favorito:</p>

<p><tt>$ vim planetalinux/proc/&lt;ubicacion&gt;/config.ini</tt></p>

<p>...donde &lt;<em>ubicacion</em>&gt; es desde luego <em>mx</em>, <em>ve</em>, <em>pe</em>, etc.</p>

<p>El formato de los feeds, nombres e imágenes de hackergotchi es bastante intuitivo:</p>

<pre>
[http://aqui.va/la/url/del/feed]
name = Juanito Pérez
face = juanito.png
</pre>

<p>La URL del blog, cuando ya tenemos el feed que vamos a agregar, no nos importa mucho. La imagen juanito.png la agregaremos
a continuación, pero sólo es necesario agregarla así, el nombre del archivo con su extensión. Planeta Linux ya sabe
dónde buscarla.</p>

<h2>Agregar hackergotchis</h2>
<p>Todos los hackergotchis deben ser, de acuerdo a los lineamientos, de no más de 95 pixeles de ancho y/o de alto, deben
estar en formato PNG y el fondo de las imágenes debe ser transparente, no blanco ni de otro color. Si es necesario y si
accedemos, hay que editarla nosotros mismos para que cumpla con los lineamientos.</p>

<p>Una vez que tenemos la imagen tal como se necesita, la colocamos en su lugar y la agregamos al depósito:</p>

<p><tt>$ mv /tmp/image001.png planetalinux/www/instancias/ubicacion/images/cabezas/juanito.png</tt><br />
<tt>$ git add planetalinux/www/instancias/ubicacion/images/cabezas/juanito.png</tt></p>

<p>...donde <em>ubicacion</em> es desde luego, alguna de las instancias.</p>

<p>Si lo que tenemos que hacer es reemplazar una imagen ya existente, sólo reescribimos la imagen previamente
existente, no sería necesario hacer <tt>svn add</tt>, pues la imagen ya fue agregada al repositorio en algún
momento.</p>

<h2>Modificar otras cosas</h2>

<p>Para estas alturas, si es necesario que edites cualquier otra cosa en el árbol, ya deberías
tener suficiente experiencia con esta onda para necesitar este HOWTO :-)</p>

<h2>Commit de los cambios</h2>

<p>Una vez que realizaste algún cambio, es necesario hacer commit. Depende de tu ubicación en el árbol,
el commit tendrá cierta recursividad. Si sólo quieres commitear los cambios de algún directorio en
específico (por alguna extraña razón):

<p><tt>$ git commit -m 'Agrego a Fulanito de las Porras' proc/mx</tt></p>

<p><strong>Siempre</strong> hay que proveer un comentario en los cambios.</p>

<p>Con un simple <tt>git commit -m 'Comentario' -a</tt> debería bastar.</p>

<h2>El historial de cambios</h2>

<p>Los diffs de cada uno de los commits es enviado a la lista de correos de Planeta Linux y son revisados
minuciosamente, así que cualquier detalle es detectable. Actúa con responsabilidad. Para ver tú mismo el
historial de los cambios en el árbol, utiliza <tt>git log</tt>.</p>

<h2>Enlaces útiles</h2>

<ul>
	<li><a href="http://log.damog.net/2008/12/two-git-tips/">Two Git tips</a></li>
</ul>

</fieldset>
</div>
</div>
<?php require("footer.php"); ?>
</body>
</html>
