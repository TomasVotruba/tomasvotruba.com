var complexity = require('../too-wordy');
var badWordInSentence = 'An abundance of highlights accede to long, complex sentences and common errors.';
var badPhrasesInSentence = 'a number of sentences are so dense and complicated that your readers will be adversely impacted';
var goodSentence = 'The good dog jumps over the bad cat.';

describe('too-wordy', function () {

  describe('a sentence filled with overly complex words', function() {
    var results = null;

    beforeEach(function() {
      results = complexity(badWordInSentence);
    });

    it('the words are noted', function () {
      expect(results).toEqual([
        { index : 3, offset : 9 },
        { index : 27, offset : 9 }
      ]);
    });
  })

  describe('a sentence with phrases that could be stated more simply', function() {
    var results = null;

    beforeEach(function() {
      results = complexity(badPhrasesInSentence);
    });

    it('the phrases are noted', function () {
      expect(results).toEqual([
        { index : 0, offset : 11 },
        { index : 87, offset : 8 }
      ]);
    });
  })

  it('should not have a problem with a short sentence', function () {
    expect(complexity(goodSentence)).toEqual([]);
  });
});
