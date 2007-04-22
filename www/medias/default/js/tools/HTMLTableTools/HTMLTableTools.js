/**
 * copyright (c) 2006 - lx barjon <lx@iassa.com>
 * copyright (c) 2006 - vb1 <http://www.javascriptfr.com/ecriremsg.aspx?id=660642>
 * class HTMLTableTools
 * version : 2.0 (16-01-2006)
 * licence : GNU GPL
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 **************************************************************************
 *
 * @author lx barjon <lx@iassa.com>
 * @author vb1 <>
 * @access public
 * @version 2.0
 */

 // index de la cellule qui engendre le tri (variable globale)
var cellIndex;

// propriete trim sur les string
String.prototype.trim = function(str) {
	str = this != window? this : str;
	/** modif de la regexp : remplac�  \s par [\s\xA0]
	 * car replace de \s ( espace ) ne fonctionne pas sous IE quand la chaine comporte des espaces cr��s
	 * par conversion de &nbsp; par la m�thode unescapeHTML() car le code de ce caractere est 160( A0 ) au lieu de 32
	 */
	return str.replace(/^[\s\xA0]+/g, '').replace(/[\s\xA0]+$/g, '');
};

// propriete removeaccent sur les string
String.prototype.removeaccent = function(str) {
    str = this != window? this : str;
    return str.replace(/[������]/g, 'a').replace(/[����]/g, 'e').replace(/[����]/g, 'i').
        replace(/[�����]/g, 'o').replace(/�/g, 'n').replace(/[����]/g, 'u').replace(/[��]/g, 'y').
            replace(/�/g, 'c').replace(/�/g, 'oe');
};

// propriete is member sur array
Array.prototype.ismember = function(search) {
    for ( var elem in this ) if (this[elem] == search) return elem;
    return -1;
};

// class HTMLTableTools
var HTMLTableTools = {};
HTMLTableTools = Class.create();
HTMLTableTools.prototype = {

	initialize: function( tableId ) {
		this.version = '2.0';
		this.dateVersion = '16-01-2006';

		Object.extend( this.options = {
			// sort options
			pathToImgs: '',
			imgUp: 'fleche_haut.png',
			imgUpActive: 'fleche_haut_active.png',
			imgDown: 'fleche_bas.png',
			imgDownActive: 'fleche_bas_active.png',
			skipCells: [],
			multiColSort: true,
			reorderCol: false,
			showSortIndex: false,
			// highlighting and select options
			highlight: true,
			highlightColumn: false,
			selectRow: true,
			multipleSelect: true
		}, arguments[1] || {} );

		if ( !this.options.highlight ) {
			this.options.highlightColumn = false;
			this.options.selectRow = false;
			this.options.multipleSelect = false;
		}

		this.error = false
		this.errorMsg = 'no error.';

		if ( !document.getElementById ) {
			this.error = true;
			this.errorMsg = "Navigateur incompatible";
			return;
		}

		if ( typeof tableId == 'undefined' ) {
			this.error = true;
			this.errorMsg = "Veuillez fournir l'ID du tableau � g�rer.";
			return;
		}

		// recup l'objet tableau correspondant a l'id fourni
		this.table = $(tableId);

		if ( !this.table ) {
			this.error = true;
			this.errorMsg = "Le tableau ayant pour ID " + tableId + " est introuvable.";
			return;
		}

		// style les lignes du tableau html
		var currentTRClass = 'rowA';
		for ( var rowCpt = 1; rowCpt < this.table.rows.length; rowCpt++ ) {
			Element.addClassName( this.table.rows[rowCpt], currentTRClass );
			if ( currentTRClass == 'rowA') currentTRClass = 'rowB';
			else currentTRClass = 'rowA';
		}

		// prepare le tableau javascript qui sera utilise pour le tri
		this.jsTable = new Array();
		for ( var rowCpt = 1; rowCpt < this.table.rows.length; rowCpt++ ) {
			this.jsTable[rowCpt-1] = new Array();
		}

		// initialisation tri
		// ******************
		// tableau pour stoquer les cellules d'entete pour pouvoir les d�placer
		this.headerTable = new Array();
		// ici on va stocker la fonction de tri correspondante au type de la cellule (sortNumber, sortString)
		this.sortFunction = new Array();
		// ici on stockera le type de donnee (date, number, string)
		var dataType = 'none';
		// contenu de la cellule
		var cellContent = '';
		// nombre total de colonne
		var totalCells = this.table.rows[0].cells.length;

		// on recup toutes les lignes dans jsTable sauf la ligne d'entete
		for ( var cellCpt = 0; cellCpt < this.table.rows[0].cells.length; cellCpt++ ) {
			// recup le contenu de la cellule
			// si la cellule est vide essaye les suivantes pour d�terminer le type de donn�e
			for ( var rowCpt = 1; rowCpt < this.table.rows.length; rowCpt++ ) {
				// evite de planter si une cellule est completement vide
				if ( !this.table.rows[rowCpt].cells[cellCpt].innerHTML ) {
						this.table.rows[rowCpt].cells[cellCpt].innerHTML = '&nbsp';
				}
				var cellContent = this.table.rows[rowCpt].cells[cellCpt].innerHTML.unescapeHTML().trim();
				if ( (cellContent != '') ) break;
			}
			// stocke la fonction de tri pour la colonne
			this.sortFunction[cellCpt] = new Array();
			// chek le type de donnee
			// date (ajouter les date EN AAAA-MM-JJ)
			if ( cellContent.match(/^\d\d[\/-]\d\d[\/-]\d\d\d\d$/) ) {
				this.sortFunction[cellCpt]["ASC"] = this._sortStringASC;
				this.sortFunction[cellCpt]["DESC"] = this._sortStringDESC;
				dataType = 'date';
			// nombre ou monnaie
			} else if ( cellContent.match(/^[0-9$�fF\.\s-]+$/) ) {
				this.sortFunction[cellCpt]["ASC"] = this._sortNumberASC;
				this.sortFunction[cellCpt]["DESC"] = this._sortNumberDESC;
				dataType = 'number';
			// texte (string)
			} else {
				this.sortFunction[cellCpt]["ASC"] = this._sortStringASC;
				this.sortFunction[cellCpt]["DESC"] = this._sortStringDESC;
				dataType = 'string';
			}
			// dernier utilis�
			this.sortFunction[cellCpt]["LAST"] = false;
			// Ordre des colonnes tri�es
			this.sortFunction[cellCpt]["INDEX"] = 0;

			// pour chaque ligne
			for ( var rowCpt = 1; rowCpt < this.table.rows.length; rowCpt++ ) {
				// recup contenu cellule
				// remplacement de replace( /<\/?[^>]+>/gi, "" ) par unescapeHTML()
				// evite de planter si une cellule est completement vide
				if ( !this.table.rows[rowCpt].cells[cellCpt].innerHTML ) {
					this.table.rows[rowCpt].cells[cellCpt].innerHTML = '&nbsp';
				}
				cellContent = this.table.rows[rowCpt].cells[cellCpt].innerHTML.unescapeHTML();
				// en fonction du type de donnee on rempli le tableau javascript qui servira au tri avec les donnees adequates
				switch (dataType) {
					case 'date':
					{
						// date ? -> on cree un objet date
						// les mois vont de 0 � 11 donc il faut retirer 1
						// (normalement cela n'avais pas d'influence sur le tri car toute les dates �taient d�cal�es)
						this.jsTable[rowCpt-1][cellCpt] = new Date(cellContent.substring(6),cellContent.substring(3,5)-1,cellContent.substring(0,2));
						break;
					}
					case 'number':
					{
						// nombre? -> on transfomre en nombre
						this.jsTable[rowCpt-1][cellCpt] = parseFloat(cellContent.replace(/[^0-9.-]/g,''));
						// si cellule vide
						if ( isNaN(this.jsTable[rowCpt-1][cellCpt] ) ) this.jsTable[rowCpt-1][cellCpt] = 0;
						break;
					}
					case 'string':
					{
						// texte ? -> on met en minuscule car la methode sort et sensible a la casse (case sensitive)
						this.jsTable[rowCpt-1][cellCpt] = cellContent.toLowerCase();
						break;
					}
				}
				// on stocke la valeur d'origine de la cellule pour pouvoir la restituer au bon moment (this.sortTable)
				this.jsTable[rowCpt-1][cellCpt+totalCells] = this.table.rows[rowCpt].cells[cellCpt].firstChild;
				// on stocke l'id de la ligne s'il existe afin de le restituer au bon endroit apr�s tri de la table
				if ( $(this.table.rows[rowCpt]).id ) {
					this.jsTable[rowCpt-1][cellCpt+(totalCells*2)] = $(this.table.rows[rowCpt]).id;
				}
			}
		}

		// ajoute les 'images boutons' (img up et down) et y accroche des event onclick
		// on cr�e les objets images (up et down) et on les stocke dans un tableau pour pouvoir les cloner
		var imgUp = new Array();
		var imgDown = new Array();
		var input = new Array();
		// creation img up
		imgUp[0] = Builder.node( 'img', {id: this.table.id + 'up0', src: this.options.pathToImgs + this.options.imgUp, style: 'cursor: pointer', className: 'imgStatus'} );
		// creation img down
		imgDown[0] = Builder.node( 'img', {id: this.table.id + 'down0', src: this.options.pathToImgs + this.options.imgDown, style: 'cursor: pointer', className: 'imgStatus'} );
		// Ajout d'un index apr�s les boutons de tri afficher l'ordre des colonnes tri�es (colonne de tri N�1 puis N�2 ...)
		var spanIndex = new Array();
		spanIndex[0] = Builder.node( 'span', {id: this.table.id + 'span0', style: 'position: absolute; display: none;', className: 'sortIndex'} );
		spanIndex[0].innerHTML = '';

		//Span pour le text
		var spanText = new Array();
		spanText[0] = Builder.node( 'span', {id: this.table.id + 'spanText0', style: 'cursor: pointer'} );
		spanText[0].innerHTML = '';

		//Input pour les filtres
		input[0] = Builder.node('input', {type:'text', size: 10});

		// pour chaque cellule
		for ( var cellCpt = 0; cellCpt < this.table.rows[0].cells.length; cellCpt++ ) {
			var skipCell = false;
			for ( var cpt = 0; cpt < this.options.skipCells.length; cpt++ ) {
				if ( this.options.skipCells[cpt]-1 == cellCpt ) {
					skipCell = true;
					break;
				}
			}
			if ( !skipCell ) {
				// si les premieres images ont ete ajoutees dans la page (DOM TREE), on clone pour l'ajout suivant (cellule suivante)
				// on modifie l'id pour avoir un code HTML valide et pour recup l'index de la cellule qui lance l'event
				if ( cellCpt > 0 ) {
					imgUp[cellCpt] = imgUp[0].cloneNode(true);
					imgUp[cellCpt].id = this.table.id + 'up' + cellCpt;
					imgDown[cellCpt] = imgDown[0].cloneNode(true);
					imgDown[cellCpt].id = this.table.id + 'down' + cellCpt;
					input[cellCpt] = input[0].cloneNode(true);
					input[cellCpt].id = this.table.id + cellCpt;
					spanIndex[cellCpt] = spanIndex[0].cloneNode(true);
					spanIndex[cellCpt].id = this.table.id + 'span' + cellCpt;
					spanText[cellCpt] = spanText[0].cloneNode(true);
					spanText[cellCpt].id = this.table.id + 'spanText' + cellCpt;
				}
				//set des input pour les filtres
				input[cellCpt].id = this.table.id + 'search[' + cellCpt + ']';
				input[cellCpt].style.visibility = 'collapse';
				input[cellCpt].cellCpt = cellCpt;

				//span pour le libelle des colonnes
				spanText[cellCpt].innerHTML = this.table.rows[0].cells[cellCpt].innerHTML;
				this.table.rows[0].cells[cellCpt].innerHTML = "";

				// on ajoute les images dans les cellules d'entete
				this.table.rows[0].cells[cellCpt].appendChild(spanText[cellCpt]);
				this.table.rows[0].cells[cellCpt].appendChild(input[cellCpt]);
				this.table.rows[0].cells[cellCpt].appendChild(imgUp[cellCpt]);
				this.table.rows[0].cells[cellCpt].appendChild(imgDown[cellCpt]);
				// on ajoute le span pour afficher l'index de tri
				this.table.rows[0].cells[cellCpt].appendChild(spanIndex[cellCpt]);
			}
		}

		// l'ajout des event se fait dans une 2eme passe car sur IE6 les events sont dupliqu�s avec cloneNode
		// cela cr�ais 2 appel successif � la methode sortTable pour les colonnes > 1
		// (les event ne se dupliquent pas sur Firefox)
		for ( var cellCpt = 0; cellCpt < this.table.rows[0].cells.length; cellCpt++ ) {
			var skipCell = false;
			for ( var cpt = 0; cpt < this.options.skipCells.length; cpt++ ) {
				if ( this.options.skipCells[cpt]-1 == cellCpt ) {
					skipCell = true;
					break;
				}
			}
			if ( !skipCell ) {
				// on recup les objets pour y ajouter les event
				var filter = $(spanText[cellCpt].id);
				var inputFilter = $(input[cellCpt].id);
				var elementUp = $(imgUp[cellCpt].id);
				var elementDown = $(imgDown[cellCpt].id);
				// ajout des event
				Event.observe( filter, 'click', this.getTextField.bindAsEventListener(this), false );
				Event.observe( inputFilter, 'click', this.filterTable.bindAsEventListener(this), false );
				Event.observe( inputFilter, 'keyup', this.filterTable.bindAsEventListener(this), false );
				Event.observe( elementUp, 'click', this.sortTable.bindAsEventListener(this), false );
				Event.observe( elementDown, 'click', this.sortTable.bindAsEventListener(this), false );
			}
			// sauvegarde l'entete
			this.headerTable[cellCpt] = this.table.rows[0].cells[cellCpt];
		}

		// initialisation highlight
		// ************************
		if ( this.options.highlight ) {
			// on recup toutes les cellules du tbaleau
			var allCells = this.table.getElementsByTagName( 'td' );
			// pour chaque cellule on ajoute les event (mouseover et mouseout)
			for ( var cpt = 0; cpt < allCells.length; cpt++ ) {
				Event.observe( allCells[cpt], 'mouseover', this.highlight.bindAsEventListener(this), false );
				Event.observe( allCells[cpt], 'mouseout', this.downlight.bindAsEventListener(this), false );
				if ( this.options.selectRow ) {
					// pour stocker les lignes selectionnees
					this.selectedRows = new Array();
					// ajout event onclick
					Event.observe( allCells[cpt], 'click', this.selectRow.bindAsEventListener(this), false );
				}
			}
		}
	},

	sortTable: function(e) {
		// recup l'element qui a declenche l'evenement (imgUp ou imgDown)
		var element = Event.element(e);
		if ( !element ) return;
		// si pas de tri multi-colonne (multi-crit�re) -> remise � z�ro
		if ( !this.options.multiColSort ) {
			for ( var cellCpt = 0; cellCpt < this.table.rows[0].cells.length; cellCpt++ ) {
				if ( this.sortFunction[cellCpt]["LAST"] ) {
					this.sortFunction[cellCpt]["LAST"] = false;
					$(this.table.id + 'up' + cellCpt).src = this.options.pathToImgs + this.options.imgUp;
					$(this.table.id + 'down' + cellCpt).src = this.options.pathToImgs + this.options.imgDown;
				}
				this.sortFunction[cellCpt]["INDEX"] = 0;
			}
		}
		// on verifie l'id de l'element pour etablir l'ordre de tri et on recup l'index correspondant
		if ( element.id.indexOf('up') != -1 ) {
			var sortOrder = 'ASC';
			cellIndex = parseInt( element.id.substr(this.table.id.length + 2) );
			// manage images
			element.src = this.options.pathToImgs + this.options.imgUpActive;
			element.nextSibling.src = this.options.pathToImgs + this.options.imgDown;
			var annul = this.sortFunction[cellIndex]['LAST'] == this.sortFunction[cellIndex][sortOrder];
			if (annul) element.src = this.options.pathToImgs + this.options.imgUp;
		} else {
			var sortOrder = 'DESC';
			cellIndex = parseInt( element.id.substr(this.table.id.length + 4) );
			// manage images
			element.src = this.options.pathToImgs + this.options.imgDownActive;
			element.previousSibling.src = this.options.pathToImgs + this.options.imgUp;
			var annul = this.sortFunction[cellIndex]['LAST'] == this.sortFunction[cellIndex][sortOrder];
			if (annul) element.src = this.options.pathToImgs + this.options.imgDown;
		}

		// Mise � jour de l'index affich� dans la colonne
		// Determine le pr�c�dent index de la colonne � trier car il faut d�crementer l'index des colonnes ayant un indice sup�rieur
		// Dernier index de la colonne � trier
		var prevIndex = this.sortFunction[cellIndex]['INDEX'];
		// on ne change l'index de la colonne que si elle �tait d�ja tri�e
		if ( (prevIndex == 0) || annul ) {
			var nmax = 0;
			for ( var cellCpt = 0; cellCpt < this.table.rows[0].cells.length; cellCpt++ ) {
				var n = this.sortFunction[cellCpt]['INDEX'];
				var nmax = n > nmax ? n : nmax;
				if ( (n > 0) && (n > prevIndex) &&  (prevIndex != 0) ){
					n--;
					this.sortFunction[cellCpt]['INDEX'] = n;
				}
			}

			if ( prevIndex == 0 ) nmax++;
			this.sortFunction[cellIndex]['INDEX'] = nmax; // La colonne actuellement tri�e prend la valeur max
		}

		// si on re-click  sur le m�me ordre de tri, desactive le tri pour la colonne
		if ( this.sortFunction[cellIndex]['LAST'] == this.sortFunction[cellIndex][sortOrder] ){
			this.sortFunction[cellIndex]['LAST'] = false;
			this.sortFunction[cellIndex]['INDEX'] = 0;
			if ( typeof this.table.rows[0].cells[cellIndex].lastChild.id != 'undefined' ) {
				this.table.rows[0].cells[cellIndex].lastChild.innerHTML = '';
				Element.hide( this.table.rows[0].cells[cellIndex].lastChild );
			}
		}
		// m�morise l'ordre des colonnes dans un tableau pour le tri
		// utile pour Firefox qui perd l'ordre des autres colonnes lors de l'appel � array.sort
		// permet de faire un tri recursif sur les colonnes
		else this.sortFunction[cellIndex]['LAST'] = this.sortFunction[cellIndex][sortOrder];

		sortedCol = new Array();
		var sortedColreorder = new Array();
		for ( var cellCpt = 0; cellCpt < this.table.rows[0].cells.length; cellCpt++ ) {
			var n = this.sortFunction[cellCpt]['INDEX'];
			if ( n > 0 ){
				sortedCol[n-1] = new Array();
				sortedCol[n-1][0] = cellCpt;
				sortedCol[n-1][1] = this.sortFunction[cellCpt]['LAST'];
				sortedColreorder[n-1] = cellCpt;
			}
		}

		var totalCells = this.table.rows[0].cells.length;

		// on recup les lignes selectionnees pour pouvoir les reselectionner apres le tri
		for ( var cellCpt = 0; cellCpt < totalCells; cellCpt++ ) {
			for ( var rowCpt = 1; rowCpt < this.table.rows.length; rowCpt++ ) {
				var currentRow = $(this.table.rows[rowCpt]);
				if ( currentRow.className && Element.hasClassName(currentRow, 'selectedRow') ) {
					this.jsTable[rowCpt-1][cellCpt+(totalCells*3)] = 'selectedRow ';
				} else {
					this.jsTable[rowCpt-1][cellCpt+(totalCells*3)] = '';
				}
			}
		}

		// deselectionne les lignes selectionnees
		for ( var rowCpt = 0; rowCpt < this.table.rows.length; rowCpt++ ) {
			var currentRow = $(this.table.rows[rowCpt]);
			if ( currentRow.className && Element.hasClassName(currentRow, 'selectedRow') ) {
				Element.removeClassName(currentRow, 'selectedRow');
			}
		}

		// Si au moins 1 colonne a trier
		if ( sortedCol.length > 0 ) {
			// Variable global car utilis� dans les fct de tri auxquels on ne peux ajouter de parametres
			cellIndex1 = sortedCol[0][0];
			// on tri le tableau js
			this.jsTable.sort(this.sortFunction[cellIndex1]['LAST']);
		// Rien � faire
		} else if ( !this.options.reorderCol ) return true;

		// on remplace le tableau actuel;
		// necessaire car le nombre de cellule change dans la boucle
		var nbcell = this.table.rows[0].cells.length;
		for ( var cellCpt = 0; cellCpt < nbcell; cellCpt++ ) {
			if ( this.options.reorderCol ) { // changement de l'ordre des colonnes
				// Ajoute les N� de colonne non tri�es � la suite
				if (sortedColreorder.length < nbcell) {
					if ( sortedColreorder.ismember(cellCpt) == -1 ) sortedColreorder.push( cellCpt );
				}
				var destcellCpt = sortedColreorder[cellCpt];
				// d�place les cellules de la ligne de titre
				if (this.table.rows[0].cells[cellCpt])
				this.table.rows[0].cells[cellCpt].parentNode.replaceChild( this.headerTable[destcellCpt], this.table.rows[0].cells[cellCpt] );
				else this.table.rows[0].cells[0].parentNode.appendChild( this.headerTable[destcellCpt] );
			}
			// pas de changement de l'ordre des colonnes
			else var destcellCpt = cellCpt;

			// N� de la colonne dans l'ordre de tri:
			if ( this.options.showSortIndex) {
				var nbstr = this.sortFunction[destcellCpt]['INDEX'] || '';
				if ( typeof this.table.rows[0].cells[cellCpt].lastChild.id != 'undefined' ) {
					( nbstr == '' ) ? Element.hide(this.table.rows[0].cells[cellCpt].lastChild) : Element.show(this.table.rows[0].cells[cellCpt].lastChild);
					this.table.rows[0].cells[cellCpt].lastChild.innerHTML = nbstr;
				}
			}

			// on remplace le tableau actuel
			for ( var rowCpt = 1; rowCpt < this.table.rows.length; rowCpt++ ) {
				this.table.rows[rowCpt].cells[cellCpt].appendChild( this.jsTable[rowCpt-1][destcellCpt+totalCells] );
				var currentRow = $(this.table.rows[rowCpt]);
				currentRow.id = this.jsTable[rowCpt-1][destcellCpt+(totalCells*2)];
				if ( cellCpt == 0 && this.jsTable[rowCpt-1][destcellCpt+(totalCells*3)] != '' ) {
					Element.addClassName( currentRow, this.jsTable[rowCpt-1][destcellCpt+(totalCells*3)] );
				}
			}
		}
	},

	_sortNumberASC: function( a, b, colIndex, n ) { // modif vb1: tri r�cursif
		colIndex = typeof(colIndex)=='number' ? colIndex : cellIndex1;
		n = n || 0;
		var test = ( a[colIndex] - b[colIndex] );
		// Si les 2 valeurs sont identiques, appelle la fct de tri de la colonne suivante
		if ( (test == 0) && (n < sortedCol.length) ) return ( sortedCol[n][1](a, b, sortedCol[n][0], n+1) );
		else return test;
	},

	_sortStringASC: function( a, b, colIndex, n ) { // modif vb1: tri r�cursif
		colIndex = typeof(colIndex) == 'number' ? colIndex : cellIndex1;
		n = n || 0;
		if ( a[colIndex] < b[colIndex] ) return -1;
		else if ( a[colIndex] > b[colIndex] ) return 1;
		else if (n < sortedCol.length) return ( sortedCol[n][1]( a, b, sortedCol[n][0], n+1 ) );
		else return 0;
	},

	// fonction de tri invers� pour �viter d'utiliser this.jsTable.reverse(); qui inverse aussi l'ordre des autres colonnes
	_sortNumberDESC: function( a, b, colIndex, n ) { // modif vb1: tri r�cursif
		colIndex = typeof(colIndex) == 'number' ? colIndex : cellIndex1;
		n = n || 0;
		var test = ( b[colIndex] - a[colIndex] );
		if ( (test == 0) && (n < sortedCol.length) ) return ( sortedCol[n][1](a, b, sortedCol[n][0], n+1) );
		else return test;
	},
	_sortStringDESC: function( a, b, colIndex, n ) {
		colIndex = typeof(colIndex) == 'number' ? colIndex : cellIndex1;
		n = n || 0;
		if ( a[colIndex] < b[colIndex] ) return 1;
		else if ( a[colIndex] > b[colIndex] ) return -1;
		else if (n < sortedCol.length) return ( sortedCol[n][1]( a, b, sortedCol[n][0], n+1 ) );
		else return 0;
	},

	getTextField: function ( e ) {
		// recup l'element qui a declenche l'evenement (imgUp ou imgDown)
		var element = Event.element(e);
		if ( !element ) return;

		cellIndex = parseInt( element.id.substr(this.table.id.length + 8) );
		element.style.visibility = 'collapse';
		element.innerHTML = '';
		var field = document.getElementById(this.table.id + 'search[' + cellIndex + ']');
		field.style.visibility = 'visible';
		field.focus();
	},

	filterTable: function ( e ) {
		var element = Event.element(e);
		if ( !element ) return;
		cellIndex = element.cellCpt;
		for ( var rowCpt = 1; rowCpt < this.table.rows.length; rowCpt++ ) {
			var currentRow = $(this.table.rows[rowCpt]);
			var string = new String(this.table.rows[rowCpt].cells[cellIndex].innerHTML).toLowerCase();
			if (string.indexOf(element.value.toLowerCase(), 0) == -1) {
				this.table.rows[rowCpt].style.visibility = "collapse";
			} else {
				this.table.rows[rowCpt].style.visibility = "visible";
			}
		}
	},

	highlight: function( e ) {
		// recup l'element qui a declenche l'evenement
		var element = window.event ? window.event.srcElement : e ? e.target : null;
		if ( !element ) return;

		// recup la cellule qui a declenche l'evenement
		element = this._ascendDOM ( element, 'td' );
		if ( !element ) return;
		// recup la ligne correspondante
		var parentRow = this._ascendDOM( element, 'tr' );
		if ( !parentRow ) return;

		// style la ligne si elle n'est pas selectionnee
		if ( !Element.hasClassName(parentRow, 'selectedRow') ) {
			Element.addClassName( parentRow, 'highlighted' );
		}
		// style la colonne
		if ( this.options.highlightColumn ) {
			var cellIndex = -1;
			// determine l'index de la cellule qui a declenche l'evenement
			for ( var cellCpt = 0; cellCpt < parentRow.cells.length; cellCpt++ ) {
				if ( element === parentRow.cells[cellCpt] ) cellIndex = cellCpt;
			}
			if ( cellIndex == -1 ) return;
			for ( var rowCpt = 0; rowCpt < this.table.rows.length; rowCpt++ ) {
				var cell = this.table.rows[rowCpt].cells[cellIndex];
				Element.addClassName( cell, 'highlighted' );
			}
		}
	},

	downlight: function( e ) {
		// recup l'element qui a declenche l'evenement
		var element = window.event ? window.event.srcElement : e ? e.target : null;
		if ( !element ) return;

		// recup la cellule qui a declenche l'evenement
		element = this._ascendDOM ( element, 'td' );
		if ( !element ) return;
		// recup la ligne correspondante
		var parentRow = this._ascendDOM( element, 'tr' );
		if ( !parentRow ) return;

		// de-style la ligne
		parentRow.className = parentRow.className.replace( /\b ?highlighted\b/, '' );
		// de-style la colonne
		if ( this.options.highlightColumn ) {
			cellIndex = element.cellIndex;
			for ( var rowCpt = 0; rowCpt < this.table.rows.length; rowCpt++ ) {
				var cell = this.table.rows[rowCpt].cells[cellIndex];
				cell.className = cell.className.replace( /\b ?highlighted\b/, '' );
			}
		}
	},

	selectRow: function( e ) {
		// recup l'element qui a declenche l'evenement
		var element = window.event ? window.event.srcElement : e ? e.target : null;
		if ( !element ) return;

		// recup la cellule qui a declenche l'evenement
		element = this._ascendDOM ( element, 'td' );
		if ( !element ) return;
		// recup la ligne correspondante
		var parentRow = this._ascendDOM( element, 'tr' );
		if ( !parentRow ) return;

		// si la ligne n'est pas selectionnee
		if ( !Element.hasClassName(parentRow, 'selectedRow') ) {
			// style la ligne
			// si selection simple
			if ( !this.options.multipleSelect ) {
				var rows = this.table.getElementsByTagName( 'tr' );
				for ( var rowCpt = 0; rowCpt < rows.length; rowCpt++ ) {
					if ( Element.hasClassName(rows[rowCpt], 'selectedRow') ) {
						rows[rowCpt].className = rows[rowCpt].className.replace( /\b ?selectedRow\b/, '' );
					}
				}
				this.selectedRows = new Array();
			}
			// on stocke l'id de la ligne selectionnee
			this.selectedRows.push( parentRow.id );
			Element.addClassName( parentRow, 'selectedRow' );
		} else {
			// de-style la ligne
			var tmpArray = this.selectedRows;
			// on met a jour les lignes selectionnee
			this.selectedRows = new Array();
			for ( var cpt = 0; cpt < tmpArray.length; cpt++ ) {
				if ( tmpArray[cpt] != parentRow.id ) this.selectedRows.push( tmpArray[cpt] );
			}
			parentRow.className = parentRow.className.replace( /\b ?selectedRow\b/, '' );
		}
		// on etablie la liste des id selectionnes (id1|id2|...|idn)
		this.selectedValues = this.selectedRows.join('|');
	},

	_ascendDOM: function( element, target ) {
		while ( element.nodeName.toLowerCase() != target && element.nodeName.toLowerCase() != 'html' ) {
			element = element.parentNode;
		}
		return ( element.nodeName.toLowerCase() == 'html' ? null : element );
	}
}