var CommentSection = React.createClass({
    getInitialState: function() {
        return {
            comments: []
        }
    },
    componentDidMount: function() {
        this.loadCommentsFromServer();
        setInterval(this.loadCommentsFromServer, 2000);
    },
    loadCommentsFromServer: function() {
        var url = this.props.url
        $.ajax({
            url: url,
            success: function (data) {
                this.setState({comments: data.comments});
            }.bind(this)
        });
    },
    render: function() {
        return (
            <CommentList comments={this.state.comments} />
        );
    }
});

var CommentList = React.createClass({
    render: function() {
        var commentNodes = this.props.comments.map(function(comment) {
            return (
                <CommentBox key={comment.id} createdAt={comment.createdAt} comment= {comment.articleComment} userName={comment.user.name} userAvatar={comment.user.avatar} userUrl={comment.user.url}/>
            );
        });
        return (
            <div>
                {commentNodes}
            </div>
        );
    }
});

var CommentBox = React.createClass({
    render: function() {
        return (
            <div className="media">
                <a className="pull-left" href={this.props.userUrl}>
                    <img className="media-object" src={this.props.userAvatar} alt={this.props.userName} width="80px"/>
                </a>
                <div className="media-body">
                    <h4 className="media-heading">
                        <a href={this.props.userUrl}>{this.props.userName}</a>
                        <small>{this.props.createdAt}</small>
                    </h4>
                    {this.props.comment}
                </div>
            </div>
        );
    }
});

window.CommentSection = CommentSection;
