(function () {
    'use strict';
    
    $('#decision-create-btn').on('click', function () {
        window.Claroline.Modal.displayForm(
            Routing.generate('laurentBulletinDecisionCreateFrom'),
            refreshPage,
            function() {}
        );
    });
    
    $('.edit-decision-btn').on('click', function () {
        var decisionId = $(this).data('decision-id');
        
        window.Claroline.Modal.displayForm(
            Routing.generate('laurentBulletinDecisionEditFrom', {'decision': decisionId}),
            refreshPage,
            function() {}
        );
    });
    
    $('.delete-decision-btn').on('click', function () {
        var decisionId = $(this).data('decision-id');

        window.Claroline.Modal.confirmRequest(
            Routing.generate(
                'laurentBulletinDecisionDelete',
                {'decision': decisionId}
            ),
            refreshPage,
            null,
            'Etes-vous sûr de vouloir supprimer cette décision ?',
            'Suppression de la décision'
        );
    });
    
    var refreshPage = function () {
        window.location.reload();
    };
})();