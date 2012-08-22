
var mooRatings = new Class({
	Implements : Options,

	options : {
		showSelectBox : false,
		container : null,
		defaultRating : null,
		onClick: null,
		disabled: false
	},

    selectBox : null,
    
    container : null,

    initialize : function(selectBox, options) {
		// set the custom options		
		this.setOptions(options);
		
		// set the selectbox
        this.selectBox = selectBox;
		
		// hide the selectbox
		if (!this.options.showSelectBox) {
			this.selectBox.setStyle('display', 'none');
		}

        // set the container
		this.setContainer();

        // add stars
        this.selectBox.getElements('option').each(
			this.createStar.bind(this)
		);

        // bind events
        this.container.addEvents({
            mouseover : this.mouseOver.bind(this),
            mouseout : this.mouseOut.bind(this),
            click : this.click.bind(this)
        });
		
		// bind change event for selectbox if shown
		if (this.options.showSelectBox) {
			this.selectBox.addEvent('change', this.change.bind(this));
		}
        
        // set the initial rating
        this.setRating(this.options.defaultRating);
    },
	
	// set the container from options or create default
	setContainer : function() {
		if ($(this.options.container)) {
			this.container = document.id(this.options.container);
			return;
		}
		this.createContainer();
	},
    
    // create the html container for the rating stars
    createContainer : function() {
        this.container = new Element('div', {
            'class' : 'ui-rating'
        }).inject(this.selectBox, 'after');
    },
    
    // create the html reating stars
    createStar : function(option) {
        new Element('a', {
            'class' : 'ui-rating-star ui-rating-empty',
            title : 'ui-rating-value-' + option.get('value'),
            value : option.get('value')
        }).inject(this.container);
    },
    
    // handle mouseover event
    mouseOver : function(e) {
		if (this.options.disabled) return;
		
		e = new Event(e);
		
        e.target.addClass('ui-rating-hover')
            .getAllPrevious()
            .addClass('ui-rating-hover');
    },
    
    // handle mouseout event
    mouseOut : function(e) {
		if (this.options.disabled) return;
		
		if (MooTools.version == '1.12')
			e = new Event(e);
		
        e.target.removeClass('ui-rating-hover')
            .getAllPrevious()
            .removeClass('ui-rating-hover');
    },
    
    // handle click event   
    click : function(e) {
		if (this.options.disabled) return;
		
		if (MooTools.version == '1.12')
			e = new Event(e);
		
		var rating = e.target.get('title').replace('ui-rating-value-', '');
        this.setRating(rating);
		this.selectBox.setProperty('value', rating);
		
		if (typeof(this.options.onClick) == 'function')
			this.options.onClick.call(this, rating);
    },

	// handle change event
	change : function(e) {
		
		if (MooTools.version == '1.12')
			e = new Event(e);
		
		var rating = e.target.get('value');
        this.setRating(rating);
	},
    
    // set the current rating
    setRating : function(rating) {
        // use selected rating if none supplied
        if (!rating) {
			rating = this.selectBox.get('value');
			// use first rating option if none selected
			if (!rating) {
				rating = this.selectBox.getElement('option[value!=]').get('value');
			}
		}
        
        // get the current selected rating star
        var current = this.container.getElement('a[title=ui-rating-value-' + rating + ']');
		
        // highlight current and previous stars in yellow
        current.setProperty('class', 'ui-rating-star ui-rating-full');
		
        current.getAllPrevious().setProperty('class', 'ui-rating-star ui-rating-full');

        // remove highlight from higher ratings
        current.getAllNext().setProperty('class', 'ui-rating-star ui-rating-empty');
		
		// synchronize the rate with the selectbox
		this.selectBox.setProperty('value', rating);
    }
	
});